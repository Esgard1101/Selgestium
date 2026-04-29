<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class NotificacionService
{
    public static function encolar(int $expedienteId, string $codigoPlantilla, array $destinatariosIds)
    {
        // Buscar la plantilla
        $plantilla = DB::table('plantillanotificacion')
            ->where('codigo', $codigoPlantilla)
            ->where('activo', true)
            ->first();

        if (!$plantilla) return;

        // Crear la cabecera de la notificación
        $notificacionId = DB::table('notificaciones')->insertGetId([
            'expediente_id' => $expedienteId,
            'plantillanotificacion_id' => $plantilla->id,
            'estado' => 'pendiente',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Registrar a cada destinatario
        foreach ($destinatariosIds as $personaId) {
            DB::table('det_notificaciondestinatario')->insert([
                'notificacion_id' => $notificacionId,
                'persona_id' => $personaId,
                'leido' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
