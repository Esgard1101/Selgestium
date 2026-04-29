<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Exception;

class ObservacionService
{
    /**
     * Registrar una observación del jurado con bloqueo RGT.
     */
    public function registrar(int $expedienteId, int $juradoId, ?int $tipoobservacionId, string $descripcion, int $actorId): void
    {
        // 1. Buscar si ya existe observación para esta ronda
        $existeRondaUno = DB::table('det_expedienteobservacion')
            ->where('expediente_id', $expedienteId)
            ->where('jurado_id', $juradoId)
            ->where('ronda', 1)
            ->exists();

        // 2. Regla RGT: Si ya existe ronda=1, bloquea rondas adicionales
        if ($existeRondaUno) {
            throw new Exception("Observaciones adicionales bloqueadas por el RGT");
        }

        // 3. Registrar Observación
        DB::transaction(function () use ($expedienteId, $juradoId, $tipoobservacionId, $descripcion, $actorId) {
            DB::table('det_expedienteobservacion')->insert([
                'expediente_id' => $expedienteId,
                'jurado_id' => $juradoId,
                'tipoobservacion_id' => $tipoobservacionId,
                'ronda' => 1,
                'descripcion' => $descripcion,
                'bloqueado' => true,
                'subsanado' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 4. Bitácora legal del expediente
            DB::table('bit_expediente')->insert([
                'expediente_id' => $expedienteId,
                'actor_id' => $actorId,
                'accion' => "Jurado (Persona ID: $juradoId) registró observación en Ronda 1",
                'ip' => request()->ip(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    /**
     * Marcar observación como subsanada.
     */
    public function marcarSubsanado(int $observacionId, int $actorId): void
    {
        DB::transaction(function () use ($observacionId, $actorId) {
            $obs = DB::table('det_expedienteobservacion')->where('id', $observacionId)->first();

            if (!$obs) {
                throw new Exception("Observación no encontrada.");
            }

            DB::table('det_expedienteobservacion')->where('id', $observacionId)->update([
                'subsanado' => true,
                'fecha_subsanacion' => now(),
                'updated_at' => now(),
            ]);

            // Bitácora de subsanación
            DB::table('bit_expediente')->insert([
                'expediente_id' => $obs->expediente_id,
                'actor_id' => $actorId,
                'accion' => "Observación ID: $observacionId fue marcada como subsanada",
                'ip' => request()->ip(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }
}
