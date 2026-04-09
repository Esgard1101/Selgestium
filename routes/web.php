<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\JuradoController;
use App\Http\Controllers\Web\PurController;
use App\Http\Controllers\Web\ObservacionController;
use App\Http\Controllers\Web\SustentacionController;
use App\Http\Controllers\Web\BitacoraAccesoController;

Route::get('/', function () {
    return redirect()->route('login');
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
    Route::get('/sustentacion/programar/{expedienteId}', [SustentacionController::class, 'showProgramar'])->name('sustentacion.programar.show');
    Route::post('/sustentacion/programar',               [SustentacionController::class, 'programar'])->name('sustentacion.programar.store');
    Route::get('/sustentacion/cerrar/{expedienteId}',    [SustentacionController::class, 'showCerrar'])->name('sustentacion.cerrar.show');
    Route::post('/sustentacion/cerrar',                  [SustentacionController::class, 'cerrar'])->name('sustentacion.cerrar.store');

    // ─── PUR: Radicación de proyectos ─────────────────────────────────────
    Route::get('/pur/radicar',  [PurController::class, 'create'])->name('pur.create');
    Route::post('/pur/radicar', [PurController::class, 'store'])->name('pur.store');

    // ─── Seguridad: Bitácora de acceso ────────────────────────────────────
    Route::get('/seguridad/bitacora-acceso', [BitacoraAccesoController::class, 'index'])->name('bitacora.acceso.index');
});
