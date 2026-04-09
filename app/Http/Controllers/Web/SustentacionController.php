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

    /**
     * Listado de expedientes aptos para programar sustentación.
     * Esta es la ruta del menú (sin parámetros).
     */
    public function indexProgramar()
    {
        try {
            $expedientes = \Illuminate\Support\Facades\DB::table('expediente as e')
                ->join('persona as p', 'p.id', '=', 'e.estudiante_id')
                ->select(
                    'e.id',
                    'e.numero_radicacion',
                    'e.titulo',
                    'e.fase_actual',
                    \Illuminate\Support\Facades\DB::raw("CONCAT(p.nombre, ' ', p.apellido) AS estudiante")
                )
                ->where('e.fase_actual', 10)
                ->whereNull('e.deleted_at')
                ->orderByDesc('e.created_at')
                ->get();
        } catch (\Throwable $e) {
            $expedientes = collect();
        }

        return view('sustentacion.seleccionar_programar', compact('expedientes'));
    }

    /**
     * Formulario de programación para un expediente específico.
     * Se llega aquí desde el listado anterior, no desde el menú.
     */
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

    /**
     * Listado de sustentaciones programadas listas para cerrar (Fase 11).
     */
    public function indexCerrar()
    {
        try {
            $expedientes = \Illuminate\Support\Facades\DB::table('expediente as e')
                ->join('persona as p', 'p.id', '=', 'e.estudiante_id')
                ->select(
                    'e.id',
                    'e.numero_radicacion',
                    'e.titulo',
                    \Illuminate\Support\Facades\DB::raw("CONCAT(p.nombre, ' ', p.apellido) AS estudiante")
                )
                ->where('e.fase_actual', 11)
                ->whereNull('e.deleted_at')
                ->orderByDesc('e.created_at')
                ->get();
        } catch (\Throwable $e) {
            $expedientes = collect();
        }

        return view('sustentacion.seleccionar_cerrar', compact('expedientes'));
    }

    public function showCerrar($expedienteId)
    {
        $expediente = \App\Models\Expediente::findOrFail($expedienteId);
        return view('sustentacion.cerrar', compact('expediente'));
    }

    public function cerrar(Request $request)
    {
        $request->validate([
            'expediente_id' => 'required|exists:expediente,id',
            'numero_acta' => 'required|string|unique:det_expedienteacta,numero_acta',
            'fecha_sustentacion' => 'required|date',
            'resultado' => 'required|string',
            'observaciones' => 'nullable|string',
        ]);

        try {
            $this->sustentacionService->cerrarExpediente($request->all());
            return redirect()->route('dashboard')->with('success', 'Expediente cerrado y acta registrada correctamente.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}
