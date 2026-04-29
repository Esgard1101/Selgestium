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
        // Se asume que el Comité Científico (fase 4) realiza la asignación
        $expedientes = DB::table('expediente')
            ->where('estado', '!=', 'cerrado')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('jurado.asignar_index', compact('expedientes'));
    }

    /**
     * Mostrar formulario de asignación para un expediente específico.
     */
    public function showAsignar($expedienteId)
    {
        $expediente = DB::table('expediente')->where('id', $expedienteId)->first();
        
        if (!$expediente) {
            return redirect()->route('jurado.asignar')->withErrors(['error' => 'Expediente no encontrado.']);
        }

        return view('jurado.asignar', compact('expediente'));
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
}
