<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\JuradoService;
use App\Services\ResolucionService;
use App\Services\PlazoService;
use App\Http\Requests\StoreJuradoRequest;
use Exception;

class JuradoController extends Controller
{
    protected $juradoService;
    protected $resolucionService;
    protected $plazoService;

    public function __construct(
        JuradoService $juradoService, 
        ResolucionService $resolucionService,
        PlazoService $plazoService
    ) {
        $this->juradoService = $juradoService;
        $this->resolucionService = $resolucionService;
        $this->plazoService = $plazoService;
    }

    /**
     * Ver la resolución de designación.
     */
    public function verResolucion($id)
    {
        try {
            $data = $this->resolucionService->obtenerDatosResolucion($id);
            return view('jurados.resolucion', $data);
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'No se pudo generar la resolución: ' . $e->getMessage()]);
        }
    }

    /**
     * Mostrar vista de asignación.
     */
    public function showAsignar()
    {
        $expedientes = \App\Models\Expediente::all();
        $personas = \App\Models\Persona::all();
        return view('jurados.asignar', compact('expedientes', 'personas'));
    }

    /**
     * Procesar la asignación de jurados.
     */
    public function asignar(StoreJuradoRequest $request)
    {
        try {
            $this->juradoService->asignarJurados(
                $request->expediente_id,
                $request->jurados
            );

            // Iniciar plazo de 15 días (Fase 2: Revisión de Jurados)
            $this->plazoService->asignarPlazo($request->expediente_id, 2);

            return redirect()->route('expediente.resolucion', $request->expediente_id)
                ->with('success', 'Jurados asignados y plazo de 15 días iniciado.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
