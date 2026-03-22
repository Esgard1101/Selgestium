<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ObservacionService;
use App\Services\PlazoService;
use Illuminate\Http\Request;
use Exception;

class ObservacionController extends Controller
{
    protected $observacionService;
    protected $plazoService;

    public function __construct(ObservacionService $observacionService, PlazoService $plazoService)
    {
        $this->observacionService = $observacionService;
        $this->plazoService = $plazoService;
    }

    public function showRegistrar()
    {
        $expedientes = \App\Models\Expediente::all();
        $jurados = \App\Models\Persona::all();

        $vencidos = [];
        foreach ($expedientes as $exp) {
            if ($this->plazoService->verificarVencimiento($exp->id)) {
                $vencidos[] = $exp->id;
            }
        }

        return view('jurados.observaciones', compact('expedientes', 'jurados', 'vencidos'));
    }

    public function registrar(\App\Http\Requests\StoreObservacionRequest $request)
    {
        try {
            $this->observacionService->registrarObservacion($request->all());
            return back()->with('success', 'Observación registrada correctamente.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
