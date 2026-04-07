<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class TwoFactorService
{
    private const EXPIRACION_MINUTOS = 10;

    /**
     * Genera un OTP de 6 dígitos para una acción transaccional crítica.
     * Invalida códigos anteriores del mismo usuario y propósito antes de crear uno nuevo.
     *
     * @param  string  $proposito  firma_jurado | aprobacion_ui | emision_resolucion
     */
    public function generarCodigo(int $userId, string $proposito, ?int $expedienteId = null): string
    {
        // Marcar códigos anteriores como usados para evitar reutilización
        DB::table('dosfactorcodigo')
            ->where('usuario_id', $userId)
            ->where('proposito', $proposito)
            ->whereNull('usado_at')
            ->update(['usado_at' => now()]);

        $codigo = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('dosfactorcodigo')->insert([
            'usuario_id'     => $userId,
            'codigo'         => $codigo,
            'proposito'      => $proposito,
            'expediente_id'  => $expedienteId,
            'expira_at'      => now()->addMinutes(self::EXPIRACION_MINUTOS),
            'created_at'     => now(),
        ]);

        return $codigo;
    }

    /**
     * Verifica un OTP transaccional.
     * Marca el código como usado si es válido.
     * Registra el intento (exitoso o fallido) en dosfactorintento.
     */
    public function verificarCodigo(int $userId, string $codigo, string $proposito): bool
    {
        $ip = request()->ip() ?? '0.0.0.0';

        $registro = DB::table('dosfactorcodigo')
            ->where('usuario_id', $userId)
            ->where('codigo', $codigo)
            ->where('proposito', $proposito)
            ->whereNull('usado_at')
            ->where('expira_at', '>', now())
            ->first();

        $exitoso = $registro !== null;

        if ($exitoso) {
            DB::table('dosfactorcodigo')
                ->where('id', $registro->id)
                ->update(['usado_at' => now()]);
        }

        $this->registrarIntento($userId, $exitoso, $ip);

        return $exitoso;
    }

    /**
     * Verifica si el usuario tiene 2FA transaccional habilitado.
     */
    public function estaHabilitadoOTP(int $userId): bool
    {
        return DB::table('dosfactorconfig')
            ->where('usuario_id', $userId)
            ->where('activo', true)
            ->whereNull('deleted_at')
            ->exists();
    }

    /**
     * Registra un intento de verificación OTP (exitoso o fallido).
     */
    public function registrarIntento(int $userId, bool $exitoso, string $ip): void
    {
        DB::table('dosfactorintento')->insert([
            'usuario_id' => $userId,
            'exitoso'    => $exitoso,
            'ip'         => mb_substr($ip, 0, 45),
            'created_at' => now(),
        ]);
    }

    /**
     * Cuenta los intentos fallidos recientes para rate limiting.
     * Ventana: últimos 15 minutos.
     */
    public function intentosFallidosRecientes(int $userId, string $ip): int
    {
        return DB::table('dosfactorintento')
            ->where('usuario_id', $userId)
            ->where('ip', $ip)
            ->where('exitoso', false)
            ->where('created_at', '>=', now()->subMinutes(15))
            ->count();
    }
}
