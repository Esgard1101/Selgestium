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
    public function indexProgramar(Request $request)
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
                ->where('e.fase_actual', '>=', 7)
                ->where('e.estado', '!=', 'cerrado')
                ->whereNull('e.deleted_at')
                ->orderByDesc('e.created_at')
                ->get();

            $resoluciones = \Illuminate\Support\Facades\DB::table('resoluciones')->get();
            
            $mes = (int) $request->input('mes', now()->month);
            $anio = (int) $request->input('anio', now()->year);
            $persona = auth()->user()->persona_id ? \Illuminate\Support\Facades\DB::table('persona')->where('id', auth()->user()->persona_id)->first() : null;
            $sucursalId = (int) $request->input('sucursal_id', $persona->sucursal_id ?? 1);

            $sustentaciones = $this->sustentacionService->calendario($sucursalId, $mes, $anio);
            $sucursales = \Illuminate\Support\Facades\DB::table('sucursal')->get();

        } catch (\Throwable $e) {
            $expedientes = collect();
            $resoluciones = collect();
            $sustentaciones = collect();
            $sucursales = collect();
            $mes = now()->month;
            $anio = now()->year;
            $sucursalId = 1;
        }

        return view('sustentacion.programar', compact('expedientes', 'resoluciones', 'sustentaciones', 'mes', 'anio', 'sucursalId', 'sucursales'));
    }

    /**
     * Formulario de programación para un expediente específico.
     */
    public function showProgramar($expedienteId)
    {
        $expediente = \Illuminate\Support\Facades\DB::table('expediente')->where('id', $expedienteId)->first();
        
        $resoluciones = \Illuminate\Support\Facades\DB::table('resoluciones')
            ->where('expediente_id', $expedienteId)
            ->get();

        return view('sustentacion.programar', compact('expediente', 'resoluciones'));
    }

    public function programar(Request $request)
    {
        $request->validate([
            'expediente_id' => 'required|exists:expediente,id',
            'fecha_hora' => 'required',
            'lugar' => 'required|string|max:255',
            'modalidad' => 'required|in:presencial,virtual',
            'enlace_virtual' => 'nullable|url',
            'resolucion_id' => 'nullable|integer'
        ]);

        try {
            $actorId = auth()->user()->persona_id ?? auth()->id();

            $this->sustentacionService->programar(
                $request->expediente_id,
                $request->fecha_hora,
                $request->lugar,
                $request->modalidad,
                $request->resolucion_id,
                $actorId
            );

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

    /**
     * Calendario de sustentaciones.
     */
    public function calendario(Request $request)
    {
        $mes = (int) $request->input('mes', now()->month);
        $anio = (int) $request->input('anio', now()->year);
        
        $persona = auth()->user()->persona_id ? \Illuminate\Support\Facades\DB::table('persona')->where('id', auth()->user()->persona_id)->first() : null;
        $sucursalId = (int) $request->input('sucursal_id', $persona->sucursal_id ?? 1);

        $sustentaciones = $this->sustentacionService->calendario($sucursalId, $mes, $anio);
        $sucursales = \Illuminate\Support\Facades\DB::table('sucursal')->get();

        return view('sustentacion.calendario', compact('sustentaciones', 'mes', 'anio', 'sucursalId', 'sucursales'));
    }
}
