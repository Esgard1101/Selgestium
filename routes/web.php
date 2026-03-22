<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\JuradoController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        $sustentaciones = app(\App\Services\SustentacionService::class)->obtenerProgramadas();
        return view('dashboard', compact('sustentaciones'));
    })->name('dashboard');

    Route::get('/expediente/asignar-jurados', [JuradoController::class, 'showAsignar'])->name('expediente.asignar-jurados.view');
    Route::post('/expediente/asignar-jurados', [JuradoController::class, 'asignar'])->name('expediente.asignar-jurados');
    Route::get('/expediente/resolucion/{id}', [JuradoController::class, 'verResolucion'])->name('expediente.resolucion');

    Route::get('/expediente/observaciones', [\App\Http\Controllers\Web\ObservacionController::class, 'showRegistrar'])->name('expediente.observaciones.view');
    Route::post('/expediente/observaciones', [\App\Http\Controllers\Web\ObservacionController::class, 'registrar'])->name('expediente.observaciones.registrar');

    // Sustentacion
    Route::get('/sustentacion/programar/{expedienteId}', [\App\Http\Controllers\Web\SustentacionController::class, 'showProgramar'])->name('sustentacion.programar.show');
    Route::post('/sustentacion/programar', [\App\Http\Controllers\Web\SustentacionController::class, 'programar'])->name('sustentacion.programar.store');
});
