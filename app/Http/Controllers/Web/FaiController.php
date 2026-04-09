<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVerificacionCreditosRequest;
use App\Services\FaiDsaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FaiController extends Controller
{
    public function __construct(private FaiDsaService $faiDsaService) {}

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
}
