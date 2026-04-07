<?php

namespace App\Listeners;

use App\Models\User;
use App\Services\BitacoraAccesoService;
use Illuminate\Auth\Events\Logout;

class RegistrarLogout
{
    public function __construct(
        private BitacoraAccesoService $bitacoraService
    ) {}

    public function handle(Logout $event): void
    {
        /** @var User|null $user */
        $user = $event->user;

        if ($user) {
            $this->bitacoraService->registrarLogout($user, request());
        }

        // Destruir variables custom de sesión
        // (Fortify/Jetstream destruye la sesión de auth por su cuenta)
        session()->forget([
            'perfil_id', 'perfil', 'sucursal_id', 'persona_id',
            'nombres', 'apellidos', 'nrodoc', 'permisos',
        ]);
    }
}
