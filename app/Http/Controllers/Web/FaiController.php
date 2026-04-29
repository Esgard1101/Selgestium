<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVerificacionCreditosRequest;
use App\Services\FaiDsaService;
use App\Services\FaiService;
use App\Services\FaiSuneduService;
use App\Services\FaiDgaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FaiController extends Controller
{
    public function __construct(
        private FaiDsaService $faiDsaService,
        private FaiService $faiService,
        private FaiSuneduService $faiSuneduService,
        private FaiDgaService $faiDgaService,
    ) {}

    /**
     * GET /fai/creditos
     * Muestra el formulario de verificación manual de créditos (RF02.1).
     * Accesible para el rol: ui
     */
    public function showVerificacionCreditos()
    {
        // Expedientes en Fase 2 (Verificar Requisitos)
        try {
            $expedientes = DB::table('expediente as e')
                ->join('persona as p', 'p.id', '=', 'e.estudiante_id')
                ->select(
                    'e.id',
                    'e.numero_radicacion',
                    'e.titulo',
                    DB::raw("CONCAT(p.nombre, ' ', p.apellido) AS estudiante")
                )
                ->where('e.fase_actual', 2)
                ->whereNull('e.deleted_at')
                ->orderByDesc('e.created_at')
                ->get();
        } catch (\Throwable $e) {
            $expedientes = collect();
        }

        // Pre-seleccionar expediente si viene por URL (?expediente_id=X)
        $expedienteIdPreseleccionado = request()->query('expediente_id');

        // Umbral global — se sobreescribe en JS cuando el usuario selecciona un expediente
        $umbralGlobal = (int) DB::table('parametro')
            ->where('codigo', 'CREDITOS_MINIMOS')
            ->whereNull('sucursal_id')
            ->whereNull('deleted_at')
            ->value('valor') ?? 160;

        return view('fai.verificacion_creditos', compact(
            'expedientes',
            'expedienteIdPreseleccionado',
            'umbralGlobal'
        ));
    }

    /**
     * POST /fai/creditos
     * Registra la verificación manual y redirige con el resultado.
     */
    public function storeVerificacionCreditos(StoreVerificacionCreditosRequest $request)
    {
        try {
            $persona = DB::table('users as u')
                ->join('persona as p', 'p.id', '=', 'u.persona_id')
                ->where('u.id', Auth::id())
                ->value('p.id');

            $resultado = DB::transaction(fn () =>
                $this->faiDsaService->verificarManual(
                    expedienteId:      (int) $request->expediente_id,
                    creditosIngresados: (int) $request->creditos,
                    validadoPorId:     (int) $persona,
                    ip:                $request->ip(),
                )
            );

            return redirect()
                ->route('fai.creditos.show')
                ->with('resultado_fai', $resultado)
                ->with('expediente_id_verificado', $request->expediente_id);

        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al registrar la verificación: ' . $e->getMessage()]);
        }
    }

    /**
     * GET /fai/panel
     * Bandeja de expedientes con verificaciones FAI pendientes (F-17).
     */
    public function panelIndex()
    {
        try {
            $expedientes = $this->faiService->verificacionesPendientes(
                (int) session('sucursal_id')
            );
        } catch (\Throwable $e) {
            $expedientes = collect();
        }

        return view('fai.bandeja', compact('expedientes'));
    }

    /**
     * GET /fai/panel/{expedienteId}
     * Tarjetas semafóricas de los 6 FAI para un expediente concreto (F-17).
     */
    public function panelExpediente(int $expedienteId)
    {
        try {
            $expediente = DB::table('expediente as e')
                ->join('persona as p', 'p.id', '=', 'e.estudiante_id')
                ->select(
                    'e.id',
                    'e.numero_radicacion',
                    'e.titulo',
                    'e.fase_actual',
                    DB::raw("CONCAT(p.nombre, ' ', p.apellido) AS estudiante")
                )
                ->where('e.id', $expedienteId)
                ->where('e.sucursal_id', (int) session('sucursal_id'))
                ->whereNull('e.deleted_at')
                ->first();

            abort_if(! $expediente, 404);

            $resultados = $this->faiService->resultadosParaExpediente($expedienteId);
        } catch (\Throwable $e) {
            abort(500, $e->getMessage());
        }


        return view('fai.panel_expediente', compact('expediente', 'resultados'));
    }

    /**
     * GET /fai/etapa2
     */
    public function showFaiEtapa2()
    {
        try {
            $expedientes = DB::table('expediente as e')
                ->join('persona as p', 'p.id', '=', 'e.estudiante_id')
                ->select(
                    'e.id',
                    'e.numero_radicacion',
                    'e.titulo',
                    'e.fase_actual',
                    'e.etapa',
                    DB::raw("CONCAT(p.nombre, ' ', p.apellido) AS estudiante")
                )
                ->where('e.sucursal_id', (int) session('sucursal_id'))
                ->whereNull('e.deleted_at')
                ->orderByDesc('e.created_at')
                ->get();
        } catch (\Throwable $e) {
            $expedientes = collect();
        }

        $expedienteId = request()->query('expediente_id');
        $expedienteSeleccionado = null;
        $resultadoSunedu = null;
        $resultadoDga = null;

        if ($expedienteId) {
            $expedienteSeleccionado = DB::table('expediente')->where('id', $expedienteId)->first();
            if ($expedienteSeleccionado) {
                $resultadoSunedu = $this->faiSuneduService->ultimoResultado($expedienteId);
                $resultadoDga = $this->faiDgaService->ultimoResultado($expedienteId);
            }
        }

        $totalEtapa2 = DB::table('expediente')->where('etapa', 'II')->whereNull('deleted_at')->count();
        $validadosHoy = DB::table('fairesultado')
            ->whereIn('fai_codigo', ['RF02.3', 'RF02.4'])
            ->whereDate('created_at', now()->toDateString())
            ->distinct('expediente_id')
            ->count();
        $pendientesManuales = DB::table('expediente')->where('etapa', 'II')->where('fase_actual', '<', 9)->count();
        $noAplicaEtapa1 = DB::table('expediente')->where('etapa', 'I')->whereNull('deleted_at')->count();

        return view('fai.etapa2', compact(
            'expedientes',
            'expedienteSeleccionado',
            'resultadoSunedu',
            'resultadoDga',
            'totalEtapa2',
            'validadosHoy',
            'pendientesManuales',
            'noAplicaEtapa1'
        ));
    }

    /**
     * POST /fai/etapa2
     */
    public function storeFaiEtapa2()
    {
        $expedienteId = request()->input('expediente_id');
        $suneduEstado = request()->input('sunedu_estado');
        $dgaEstado = request()->input('dga_estado');

        if (!$expedienteId) {
            return back()->withErrors(['error' => 'Expediente no especificado.']);
        }

        try {
            $persona = DB::table('users as u')
                ->join('persona as p', 'p.id', '=', 'u.persona_id')
                ->where('u.id', Auth::id())
                ->value('p.id');

            DB::transaction(function () use ($expedienteId, $suneduEstado, $dgaEstado, $persona) {
                DB::table('fairesultado')->insert([
                    'expediente_id'   => $expedienteId,
                    'fai_codigo'      => 'RF02.3',
                    'apifuente_id'    => 4,
                    'estado'          => $suneduEstado,
                    'valor_obtenido'  => 'Grado Bachiller ' . $suneduEstado,
                    'valor_umbral'    => 'Requerido',
                    'respuesta_raw'   => json_encode(['modo' => 'manual']),
                    'validado_por_id'  => $persona,
                    'ip_actor'         => request()->ip(),
                    'verificado_at'    => now(),
                    'created_at'       => now(),
                ]);

                DB::table('fairesultado')->insert([
                    'expediente_id'   => $expedienteId,
                    'fai_codigo'      => 'RF02.4',
                    'apifuente_id'    => 2,
                    'estado'          => $dgaEstado,
                    'valor_obtenido'  => 'Voucher ' . $dgaEstado,
                    'valor_umbral'    => 'Requerido',
                    'respuesta_raw'   => json_encode(['modo' => 'manual']),
                    'validado_por_id'  => $persona,
                    'ip_actor'         => request()->ip(),
                    'verificado_at'    => now(),
                    'created_at'       => now(),
                ]);
            });

            return redirect()
                ->route('fai.etapa2.index', ['expediente_id' => $expedienteId])
                ->with('success', 'Requisitos FAI registrados satisfactoriamente.');

        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Error al registrar: ' . $e->getMessage()]);
        }
    }
}
