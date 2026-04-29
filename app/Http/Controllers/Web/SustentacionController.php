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
     * Listado de expedientes listos para ingresar acta.
     */
    public function indexCerrar()
    {
        try {
            $expedientes = \Illuminate\Support\Facades\DB::table('expediente as e')
                ->join('sustentacion as s', 's.expediente_id', '=', 'e.id')
                ->join('persona as p', 'p.id', '=', 'e.estudiante_id')
                ->select(
                    'e.id',
                    'e.numero_radicacion',
                    'e.titulo',
                    's.id as sustentacion_id',
                    \Illuminate\Support\Facades\DB::raw("CONCAT(p.nombre, ' ', p.apellido) AS estudiante")
                )
                ->where('s.estado', 'programado')
                ->whereNull('s.deleted_at')
                ->get();
        } catch (\Throwable $e) {
            $expedientes = collect();
        }

        return view('sustentacion.seleccionar_cerrar', compact('expedientes'));
    }

    /**
     * Formulario de Acta de Sustentación.
     */
    public function showCerrar($expedienteId)
    {
        $expediente = \App\Models\Expediente::findOrFail($expedienteId);
        
        $sustentacion = \Illuminate\Support\Facades\DB::table('sustentacion')
            ->where('expediente_id', $expedienteId)
            ->where('estado', 'programado')
            ->first();

        if (!$sustentacion) {
            // Si ya está finalizada, buscamos la última guardada para modo readonly
            $sustentacion = \Illuminate\Support\Facades\DB::table('sustentacion')
                ->where('expediente_id', $expedienteId)
                ->orderByDesc('created_at')
                ->first();
        }

        $acta = null;
        if ($sustentacion) {
            $acta = \Illuminate\Support\Facades\DB::table('actasustentacion')
                ->where('sustentacion_id', $sustentacion->id)
                ->first();
        }

        // Determinar si el usuario autenticado es el presidente del jurado
        $esPresidente = false;
        $personaId = auth()->user()->persona_id;
        if ($personaId) {
            $juradoPresidente = \Illuminate\Support\Facades\DB::table('det_expedientejurado')
                ->where('expediente_id', $expedienteId)
                ->where('jurado_id', $personaId)
                ->where('rol_jurado', 'presidente')
                ->first();
            if ($juradoPresidente) {
                $esPresidente = true;
            }
        }

        // Si el usuario es administrador (rol admin), también le permitimos cerrar como fallback de pruebas
        $esAdmin = \Illuminate\Support\Facades\DB::table('rolpersona')
            ->where('persona_id', $personaId)
            ->where('rol_id', 1) // 1 suele ser Administrador
            ->exists();
            
        if ($esAdmin) {
            $esPresidente = true;
        }

        $jurados = \Illuminate\Support\Facades\DB::table('det_expedientejurado as dej')
            ->join('persona as p', 'p.id', '=', 'dej.jurado_id')
            ->select(
                \Illuminate\Support\Facades\DB::raw("CONCAT(p.nombre, ' ', p.apellido) AS nombre_completo"),
                'dej.rol_jurado'
            )
            ->where('dej.expediente_id', $expedienteId)
            ->get();

        $nombrePresidente = $jurados->where('rol_jurado', 'presidente')->first()->nombre_completo ?? 'No asignado';
        $nombreSecretario = $jurados->where('rol_jurado', 'secretario')->first()->nombre_completo ?? 'No asignado';
        $nombreVocal = $jurados->where('rol_jurado', 'vocal')->first()->nombre_completo ?? 'No asignado';

        return view('sustentacion.acta', compact(
            'expediente', 
            'sustentacion', 
            'acta', 
            'esPresidente',
            'nombrePresidente',
            'nombreSecretario',
            'nombreVocal'
        ));
    }

    /**
     * Registra el acta de sustentación.
     */
    public function cerrar(Request $request)
    {
        $request->validate([
            'sustentacion_id' => 'required|integer',
            'nota1' => 'required|numeric|min:0|max:20',
            'nota2' => 'required|numeric|min:0|max:20',
            'nota3' => 'required|numeric|min:0|max:20',
            'resultado' => 'required|string|in:aprobado,desaprobado',
            'observaciones' => 'nullable|string',
        ]);

        try {
            $actorId = auth()->user()->persona_id ?? auth()->id();
            $notas = [$request->nota1, $request->nota2, $request->nota3];

            $this->sustentacionService->registrarActa(
                $request->sustentacion_id,
                $notas,
                $request->resultado,
                $request->observaciones,
                $actorId
            );

            return redirect()->route('dashboard')->with('success', 'Acta registrada y expediente cerrado exitosamente.');
        } catch (\Exception $e) {
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
