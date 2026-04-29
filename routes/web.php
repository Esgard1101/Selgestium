<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\JuradoController;
use App\Http\Controllers\Web\PurController;
use App\Http\Controllers\Web\ObservacionController;
use App\Http\Controllers\Web\SustentacionController;
use App\Http\Controllers\Web\BitacoraAccesoController;
use App\Http\Controllers\Web\FaiController;
use App\Http\Controllers\Web\PlazoController;

Route::get('/', function () {
    return redirect()->route('login');
});

// ─── AJAX: endpoints de búsqueda (auth requerida, sin verificar.permiso) ─────
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verificar.sesion'])
    ->prefix('ajax')
    ->name('ajax.')
    ->group(function () {

        // Búsqueda de personas (docentes + estudiantes) — para x-autocomplete
        Route::get('/personas/search', function (\Illuminate\Http\Request $request) {
            $q = trim($request->get('q', ''));
            if (strlen($q) < 2) return response()->json([]);

            $resultados = \Illuminate\Support\Facades\DB::table('persona')
                ->select(
                    'id',
                    'nombre',
                    'apellido',
                    'dni'
                )
                ->where(function ($query) use ($q) {
                    $query->whereRaw("CONCAT(nombre, ' ', apellido) ILIKE ?", ["%{$q}%"])
                        ->orWhere('dni', 'ILIKE', "%{$q}%");
                })
                ->whereNull('deleted_at')
                ->limit(10)
                ->get()
                ->map(fn($p) => [
                    'id'       => $p->id,
                    'label'    => "{$p->nombre} {$p->apellido}",
                    'sublabel' => 'DNI · ' . $p->dni,
                ]);

            return response()->json($resultados);
        })->name('personas.search');
    });

// ─── Rutas del ERP (requieren auth + sesión enriquecida + permiso) ────────────
// Cada ruta devuelve HTML Blade completo — nunca JSON de UI.
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'verificar.sesion',
    'verificar.permiso',
])->group(function () {

    Route::get('/dashboard', function () {
        $sustentaciones = app(\App\Services\SustentacionService::class)->obtenerProgramadas();
        
        $plazos = DB::table('controlplazo')->get();
        $vencidosHoy = 0;
        $porVencer3Dias = 0;
        $art123dHabilitados = 0;
        $ahora = now();

        foreach ($plazos as $plazo) {
            $fechaVenc = \Carbon\Carbon::parse($plazo->fecha_vencimiento);
            if ($plazo->vencido || $fechaVenc->isPast()) {
                if ($fechaVenc->isToday()) {
                    $vencidosHoy++;
                }
            }
            if (!$plazo->vencido && !$fechaVenc->isPast()) {
                $dias = 0;
                $actual = $ahora->copy();
                while ($actual->lt($fechaVenc)) {
                    $actual->addDay();
                    if (!$actual->isWeekend()) {
                        $dias++;
                    }
                }
                if ($dias <= 3 && $dias > 0) {
                    $porVencer3Dias++;
                }
            }
            if ($plazo->art123d_habilitado) {
                $art123dHabilitados++;
            }
        }

        return view('dashboard', compact(
            'sustentaciones',
            'vencidosHoy',
            'porVencer3Dias',
            'art123dHabilitados'
        ));
    })->name('dashboard');

    // ─── Jurado: Asignación y Revisiones (F-12) ───────────────────────────
    Route::get('/jurado/asignar', [JuradoController::class, 'indexAsignar'])
        ->name('jurado.asignar');
    Route::get('/jurado/asignar/{expedienteId}', [JuradoController::class, 'showAsignar'])
        ->name('jurado.asignar.show');
    Route::post('/jurado/asignar', [JuradoController::class, 'asignar'])
        ->name('jurado.asignar.store');
    Route::get('/jurado/mis-revisiones', [JuradoController::class, 'misRevisiones'])
        ->name('jurado.mis_revisiones');

    Route::get('/expediente/resolucion/{id}', [JuradoController::class, 'verResolucion'])
        ->name('expediente.resolucion');

    // ─── Expediente: Observaciones de jurados ─────────────────────────────
    Route::get('/jurado/observaciones/{expedienteId}', [ObservacionController::class, 'showObservaciones'])
        ->name('jurado.observaciones.show');
    Route::post('/jurado/observaciones', [ObservacionController::class, 'storeObservacion'])
        ->name('jurado.observaciones.store');

    // ─── Sustentación ─────────────────────────────────────────────────────
    Route::get('/sustentacion/programar',              [SustentacionController::class, 'indexProgramar'])->name('sustentacion.programar.index');
    Route::get('/sustentacion/programar/{expedienteId}', [SustentacionController::class, 'showProgramar'])->name('sustentacion.programar.show');
    Route::post('/sustentacion/programar',               [SustentacionController::class, 'programar'])->name('sustentacion.programar.store');
    Route::get('/sustentacion/cerrar',               [SustentacionController::class, 'indexCerrar'])->name('sustentacion.cerrar.index');
    Route::get('/sustentacion/cerrar/{expedienteId}', [SustentacionController::class, 'showCerrar'])->name('sustentacion.cerrar.show');
    Route::post('/sustentacion/cerrar',                  [SustentacionController::class, 'cerrar'])->name('sustentacion.cerrar.store');

    // ─── PUR: Radicación de proyectos ─────────────────────────────────────
    Route::get('/pur/radicar',  [PurController::class, 'create'])->name('pur.create');
    Route::post('/pur/radicar', [PurController::class, 'store'])->name('pur.store');

    // ─── Seguridad: Bitácora de acceso ────────────────────────────────────
    Route::get('/seguridad/bitacora-acceso', [BitacoraAccesoController::class, 'index'])->name('bitacora.acceso.index');

    // ─── Control de Plazos (F-21) ─────────────────────────────────────────
    Route::get('/plazos', [PlazoController::class, 'dashboard'])->name('plazo.dashboard');

    // ─── FAI: Verificaciones administrativas (rol: ui) ────────────────────
    // RF02.1 — Créditos académicos (manual en Sprint 1; API en Sprint 2)
    Route::get('/fai/creditos',  [FaiController::class, 'showVerificacionCreditos'])->name('fai.creditos.show');
    Route::post('/fai/creditos', [FaiController::class, 'storeVerificacionCreditos'])->name('fai.creditos.store');

    // F-14 — FAI Etapa II (Bachiller + Voucher de Sustentación)
    Route::get('/fai/etapa2', [FaiController::class, 'showFaiEtapa2'])->name('fai.etapa2.index');
    Route::post('/fai/etapa2', [FaiController::class, 'storeFaiEtapa2'])->name('fai.etapa2.store');

    // F-17 — Panel FAI unificado (rol: ui, cc, admin)
    Route::get('/fai/panel',                [FaiController::class, 'panelIndex'])     ->name('fai.panel.index');
    Route::get('/fai/panel/{expedienteId}', [FaiController::class, 'panelExpediente'])->name('fai.panel.show');
});

Route::post('/pur', [PurController::class, 'store'])->name('pur.store');

Route::get('/pur', [PurController::class, 'index'])->name('pur.index');
Route::get('/pur/create', [PurController::class, 'create'])->name('pur.create');
Route::post('/pur/radicar', [PurController::class, 'store'])->name('pur.store');
Route::get('/pur/{id}', [PurController::class, 'show'])->name('pur.show');
Route::get('/pur/{id}/descargar', [\App\Http\Controllers\Web\PurController::class, 'descargar'])->name('pur.descargar');
