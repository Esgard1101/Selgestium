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

    public function registrar(Request $request)
    {
        $request->validate([
            'expediente_id' => 'required|integer|exists:expediente,id',
            'jurado_id' => 'required|integer|exists:persona,id',
            'ronda' => 'required|integer|min:1',
            'descripcion' => 'required|string',
            'tipo_veredicto' => 'required|string|in:aprobado,observado',
        ]);

        try {
            $this->observacionService->registrarObservacion($request->all());
            return back()->with('success', 'Observación registrada correctamente.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
