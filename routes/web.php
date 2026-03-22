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
        return view('dashboard');
    })->name('dashboard');

    Route::get('/expediente/asignar-jurados', [JuradoController::class, 'showAsignar'])->name('expediente.asignar-jurados.view');
    Route::post('/expediente/asignar-jurados', [JuradoController::class, 'asignar'])->name('expediente.asignar-jurados');
});
