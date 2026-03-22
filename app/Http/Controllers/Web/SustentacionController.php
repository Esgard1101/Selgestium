<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\SustentacionService;
use Illuminate\Http\Request;
use Exception;

class SustentacionController extends Controller
{
    protected $sustentacionService;

    public function __construct(SustentacionService $sustentacionService)
    {
        $this->sustentacionService = $sustentacionService;
    }

    public function showProgramar($expedienteId)
    {
        $expediente = \App\Models\Expediente::findOrFail($expedienteId);
        return view('sustentacion.programar', compact('expediente'));
    }

    public function programar(Request $request)
    {
        $request->validate([
            'expediente_id' => 'required|exists:expediente,id',
            'fecha' => 'required|date',
            'hora' => 'required',
            'lugar' => 'required|string|max:255',
        ]);

        try {
            $this->sustentacionService->programar($request->all());
            return redirect()->route('dashboard')->with('success', 'Sustentación programada exitosamente.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}
