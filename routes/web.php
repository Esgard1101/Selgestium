<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\PurController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware(['auth'])->group(function () {

    // Ruta para MOSTRAR el formulario (GET)
    Route::get('/pur/radicar', [PurController::class, 'create'])->name('pur.create');

    // Ruta para PROCESAR el formulario y guardar el PDF (POST)
    Route::post('/pur/radicar', [PurController::class, 'store'])->name('pur.store');
});
