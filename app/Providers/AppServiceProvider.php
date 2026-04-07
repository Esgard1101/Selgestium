<?php

namespace App\Providers;

use App\Listeners\EnriquecerSesionLogin;
use App\Listeners\RegistrarLogout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(Login::class, EnriquecerSesionLogin::class);
        Event::listen(Logout::class, RegistrarLogout::class);
    }
}
