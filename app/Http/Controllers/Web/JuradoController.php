<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\JuradoService;
use App\Http\Requests\StoreAsignacionJuradoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class JuradoController extends Controller
{
    protected $juradoService;

    public function __construct(JuradoService $juradoService)
    {
        $this->juradoService = $juradoService;
    }

    /**
     * Selector de expedientes para asignar jurado.
     */
    public function indexAsignar()
    {
        $expedientes = DB::table('expediente')
            ->leftJoin('persona as est', 'expediente.estudiante_id', '=', 'est.id')
            ->select('expediente.*', DB::raw("CONCAT(est.nombre, ' ', est.apellido) as estudiante_nombre"))
            ->where('expediente.estado', '!=', 'cerrado')
            ->orderBy('created_at', 'desc')
            ->get();

        $pendientesCount = DB::table('expediente')->where('estado', '!=', 'cerrado')->count();
        $juradosActivosCount = DB::table('rolpersona')->where('rol_id', 8)->count();
        $resolucionesCount = DB::table('resoluciones')->count();

        return view('jurado.asignar_index', compact('expedientes', 'pendientesCount', 'juradosActivosCount', 'resolucionesCount'));
    }

    /**
     * Mostrar formulario de asignación para un expediente específico.
     */
    public function showAsignar($expedienteId)
    {
        $expediente = DB::table('expediente')
            ->leftJoin('persona as est', 'expediente.estudiante_id', '=', 'est.id')
            ->leftJoin('persona as ase', 'expediente.asesor_id', '=', 'ase.id')
            ->leftJoin('sucursal', 'expediente.sucursal_id', '=', 'sucursal.id')
            ->select(
                'expediente.*', 
                DB::raw("CONCAT(est.nombre, ' ', est.apellido) as estudiante_nombre"), 
                DB::raw("CONCAT(ase.nombre, ' ', ase.apellido) as asesor_nombre"),
                'sucursal.descripcion as sucursal_nombre'
            )
            ->where('expediente.id', $expedienteId)
            ->first();
        
        if (!$expediente) {
            return redirect()->route('jurado.asignar')->withErrors(['error' => 'Expediente no encontrado.']);
        }

        $pendientesCount = DB::table('expediente')->where('estado', '!=', 'cerrado')->count();
        $juradosActivosCount = DB::table('rolpersona')->where('rol_id', 8)->count();
        $resolucionesCount = DB::table('resoluciones')->count();

        return view('jurado.asignar', compact('expediente', 'pendientesCount', 'juradosActivosCount', 'resolucionesCount'));
    }

    /**
     * Procesar la asignación de jurados y resolución.
     */
    public function asignar(StoreAsignacionJuradoRequest $request)
    {
        try {
            $actorId = auth()->user()->persona_id ?? auth()->id();

            // 1. Registrar Resolución
            $this->juradoService->registrarResolucion(
                $request->expediente_id,
                $request->numero_resolucion,
                $request->fecha_emision,
                $actorId
            );

            // 2. Asignar Jurados
            $jurados = [
                'presidente' => $request->presidente_id,
                'secretario' => $request->secretario_id,
                'vocal'      => $request->vocal_id,
            ];

            $this->juradoService->asignar(
                $request->expediente_id,
                $jurados,
                $actorId
            );

            return redirect()->route('jurado.asignar')
                ->with('success', 'Jurados asignados y resolución registrada exitosamente.');
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Listado de expedientes asignados al jurado autenticado.
     */
    public function misRevisiones()
    {
        $personaId = auth()->user()->persona_id ?? null;

        if (!$personaId) {
            return redirect()->route('dashboard')->withErrors(['error' => 'Usuario no vinculado a una persona.']);
        }

        $expedientes = DB::table('det_expedientejurado')
            ->join('expediente', 'det_expedientejurado.expediente_id', '=', 'expediente.id')
            ->where('det_expedientejurado.jurado_id', $personaId)
            ->where('det_expedientejurado.activo', true)
            ->select('expediente.*', 'det_expedientejurado.rol_jurado', 'det_expedientejurado.aprobado')
            ->orderBy('expediente.created_at', 'desc')
            ->get();

        return view('jurado.mis_revisiones', compact('expedientes'));
    }

    /**
     * Ver la resolución de designación.
     */
    public function verResolucion($id)
    {
        try {
            $resolucion = DB::table('resoluciones')->where('id', $id)->first();
            if (!$resolucion) {
                throw new Exception("La resolución no existe.");
            }
            return view('jurado.resolucion', compact('resolucion'));
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'No se pudo visualizar la resolución: ' . $e->getMessage()]);
        }
    }

    /**
     * Cargar vista de emisión de veredicto.
     */
    public function veredicto($expedienteId)
    {
        $expediente = DB::table('expediente')
            ->leftJoin('persona as est', 'expediente.estudiante_id', '=', 'est.id')
            ->select('expediente.*', DB::raw("CONCAT(est.nombre, ' ', est.apellido) as estudiante_nombre"))
            ->where('expediente.id', $expedienteId)
            ->first();

        if (!$expediente) {
            abort(404, 'Expediente no encontrado.');
        }

        $jurados = DB::table('det_expedientejurado')
            ->join('persona', 'det_expedientejurado.jurado_id', '=', 'persona.id')
            ->where('det_expedientejurado.expediente_id', $expedienteId)
            ->where('det_expedientejurado.activo', true)
            ->select('det_expedientejurado.*', 'persona.nombre', 'persona.apellido')
            ->get();

        $juradoActual = $jurados->where('jurado_id', auth()->user()->persona_id)->first();

        return view('jurado.veredicto', compact('expediente', 'jurados', 'juradoActual'));
    }

    /**
     * Encolar OTP de 2FA para la firma.
     */
    public function generarOtp(Request $request)
    {
        $juradoId = auth()->user()->persona_id ?? null;
        $user = DB::table('users')->where('persona_id', $juradoId)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado.'], 404);
        }

        $otp = app(\App\Services\TwoFactorService::class)->generarCodigo($user->id, 'firma_jurado', $request->expediente_id);

        \Illuminate\Support\Facades\Log::info("Código 2FA emitido para Jurado {$juradoId}: {$otp}");

        return response()->json(['success' => true, 'message' => 'Código enviado satisfactoriamente.']);
    }

    /**
     * Procesar el voto del jurado.
     */
    public function storeVeredicto(Request $request)
    {
        $request->validate([
            'expediente_id' => 'required|integer',
            'voto'          => 'required|boolean',
            'codigo_2fa'    => 'required|string'
        ]);

        try {
            $juradoId = auth()->user()->persona_id ?? auth()->id();

            $this->juradoService->registrarVeredicto(
                $request->expediente_id,
                $juradoId,
                (bool) $request->voto,
                $request->codigo_2fa,
                $request->ip()
            );

            return redirect()->route('jurado.mis_revisiones')->with('success', 'Veredicto registrado con éxito.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
