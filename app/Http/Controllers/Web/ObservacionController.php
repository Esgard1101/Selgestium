<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ObservacionService;
use Illuminate\Http\Request;
use Exception;

class ObservacionController extends Controller
{
    protected $observacionService;

    public function __construct(ObservacionService $observacionService)
    {
        $this->observacionService = $observacionService;
    }

    public function showRegistrar()
    {
        $expedientes = \App\Models\Expediente::all();
        $jurados = \App\Models\Persona::all();
        return view('jurados.observaciones', compact('expedientes', 'jurados'));
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
