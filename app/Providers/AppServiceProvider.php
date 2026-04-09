<?php

namespace App\Providers;

use App\Listeners\EnriquecerSesionLogin;
use App\Listeners\RegistrarLogout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Event::listen(Login::class, EnriquecerSesionLogin::class);
        Event::listen(Logout::class, RegistrarLogout::class);

        // Carga los ítems de menú del usuario autenticado para el sidebar.
        // Solo consulta la BD si hay permisos en sesión y la tabla existe.
        View::composer('layouts.app', function ($view) {
            $permisos = session('permisos', []);

            $menuItems = collect();

            if (!empty($permisos)) {
                try {
                    $menuItems = DB::table('opcionmenu')
                        ->whereIn('id', $permisos)
                        ->where('activo', true)
                        ->orderBy('grupo')
                        ->orderBy('orden')
                        ->get();
                } catch (\Throwable) {
                    // Tabla aún no existe (ej. primera migración en progreso)
                    $menuItems = collect();
                }
            }

            $view->with('menuItems', $menuItems->groupBy('grupo'));
        });
    }
}
