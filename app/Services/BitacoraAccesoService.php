<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BitacoraAccesoService
{
    /**
     * Registra un login exitoso en bit_accesousuario.
     * INSERT directo — tabla inmutable, sin usar DataBaseTrait.
     */
    public function registrarLogin(User $user, Request $request): void
    {
        $this->insertar($user->id, 'login', $request, session('sucursal_id'));
    }

    /**
     * Registra un logout en bit_accesousuario.
     */
    public function registrarLogout(User $user, Request $request): void
    {
        $this->insertar($user->id, 'logout', $request, session('sucursal_id'));
    }

    /**
     * Registra un intento de login fallido.
     * usuario_id puede ser null si el email no existe en el sistema.
     */
    public function registrarLoginFallido(string $email, Request $request): void
    {
        $usuario = DB::table('users')->where('email', $email)->value('id');

        $this->insertar($usuario, 'login_fallido', $request, null);
    }

    /**
     * Registra cierre por sesión expirada (llamado desde VerificarSesion).
     */
    public function registrarSesionExpirada(int $usuarioId, Request $request): void
    {
        $this->insertar($usuarioId, 'sesion_expirada', $request, session('sucursal_id'));
    }

    private function insertar(?int $usuarioId, string $accion, Request $request, ?int $sucursalId): void
    {
        DB::table('bit_accesousuario')->insert([
            'usuario_id'  => $usuarioId,
            'accion'      => $accion,
            'ip'          => $request->ip() ?? '0.0.0.0',
            'user_agent'  => mb_substr($request->userAgent() ?? '', 0, 500),
            'sucursal_id' => $sucursalId,
            'created_at'  => now(),
        ]);
    }
}
