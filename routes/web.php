<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\JuradoController;
use App\Http\Controllers\Web\PurController;
use App\Http\Controllers\Web\ObservacionController;
use App\Http\Controllers\Web\SustentacionController;
use App\Http\Controllers\Web\BitacoraAccesoController;
use App\Http\Controllers\Web\FaiController;

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
                    'nombres',
                    'apellido_paterno',
                    'apellido_materno',
                    'numero_documento',
                    'tipo_persona'
                )
                ->where(function ($query) use ($q) {
                    $query->whereRaw("CONCAT(nombres, ' ', apellido_paterno, ' ', apellido_materno) ILIKE ?", ["%{$q}%"])
                        ->orWhere('numero_documento', 'ILIKE', "%{$q}%");
                })
                ->whereNull('deleted_at')
                ->where('activo', true)
                ->limit(10)
                ->get()
                ->map(fn($p) => [
                    'id'       => $p->id,
                    'label'    => "{$p->nombres} {$p->apellido_paterno} {$p->apellido_materno}",
                    'sublabel' => ucfirst($p->tipo_persona) . ' · ' . $p->numero_documento,
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
        return view('dashboard', compact('sustentaciones'));
    })->name('dashboard');

    // ─── Expediente: Asignación de jurados (UI) ───────────────────────────
    Route::get('/expediente/asignar-jurados', [JuradoController::class, 'showAsignar'])
        ->name('expediente.asignar-jurados.view');
    Route::post('/expediente/asignar-jurados', [JuradoController::class, 'asignar'])
        ->name('expediente.asignar-jurados');
    Route::get('/expediente/resolucion/{id}', [JuradoController::class, 'verResolucion'])
        ->name('expediente.resolucion');

    // ─── Expediente: Observaciones de jurados ─────────────────────────────
    Route::get('/expediente/observaciones',  [ObservacionController::class, 'showRegistrar'])->name('expediente.observaciones.view');
    Route::post('/expediente/observaciones', [ObservacionController::class, 'registrar'])->name('expediente.observaciones.registrar');

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

    // ─── FAI: Verificaciones administrativas (rol: ui) ────────────────────
    // RF02.1 — Créditos académicos (manual en Sprint 1; API en Sprint 2)
    Route::get('/fai/creditos',  [FaiController::class, 'showVerificacionCreditos'])->name('fai.creditos.show');
    Route::post('/fai/creditos', [FaiController::class, 'storeVerificacionCreditos'])->name('fai.creditos.store');

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
