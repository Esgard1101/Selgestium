<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ObservacionService;
use App\Http\Requests\StoreObservacionRequest;
use Illuminate\Support\Facades\DB;
use Exception;

class ObservacionController extends Controller
{
    protected $observacionService;

    public function __construct(ObservacionService $observacionService)
    {
        $this->observacionService = $observacionService;
    }

    /**
     * Mostrar panel de revisión y observaciones.
     */
    public function showObservaciones($expedienteId)
    {
        $expediente = DB::table('expediente')
            ->leftJoin('persona as est', 'expediente.estudiante_id', '=', 'est.id')
            ->select('expediente.*', DB::raw("CONCAT(est.nombre, ' ', est.apellido) as estudiante_nombre"))
            ->where('expediente.id', $expedienteId)
            ->first();

        if (!$expediente) {
            abort(404, 'Expediente no encontrado.');
        }

        $juradoId = auth()->user()->persona_id ?? auth()->id();

        $observaciones = DB::table('det_expedienteobservacion')
            ->where('expediente_id', $expedienteId)
            ->where('jurado_id', $juradoId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Bloqueado si ya existe ronda 1
        $esBloqueado = DB::table('det_expedienteobservacion')
            ->where('expediente_id', $expedienteId)
            ->where('jurado_id', $juradoId)
            ->where('ronda', 1)
            ->exists();

        return view('jurado.observaciones', compact('expediente', 'observaciones', 'esBloqueado'));
    }

    /**
     * Registrar observación del jurado.
     */
    public function storeObservacion(StoreObservacionRequest $request)
    {
        try {
            $juradoId = auth()->user()->persona_id ?? auth()->id();
            $actorId = auth()->user()->persona_id ?? auth()->id();

            $this->observacionService->registrar(
                $request->expediente_id,
                $juradoId,
                $request->tipoobservacion_id,
                $request->descripcion,
                $actorId
            );

            return back()->with('success', 'Observación registrada exitosamente.');

        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}
