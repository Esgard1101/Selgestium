<?php

namespace App\Listeners;

use App\Models\User;
use App\Services\BitacoraAccesoService;
use App\Services\RoleService;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;

class EnriquecerSesionLogin
{
    public function __construct(
        private RoleService $roleService,
        private BitacoraAccesoService $bitacoraService
    ) {}

    public function handle(Login $event): void
    {
        /** @var User $user */
        $user = $event->user;

        // 1. Cargar sesión enriquecida con perfil, sucursal y permisos
        $this->roleService->loadSessionData($user);

        // 2. Actualizar último login
        DB::table('users')
            ->where('id', $user->id)
            ->update(['ultimo_login_at' => now()]);

        // 3. Registrar en bitácora de acceso
        $this->bitacoraService->registrarLogin($user, request());
    }
}
