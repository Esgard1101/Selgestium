<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Inserta una fila de faiconfig por cada verificación FAI (RF02.1–RF02.6)
 * para la sucursal FACHSE demo, en modo_operacion='manual'.
 *
 * Sprint 2 — cuando las APIs externas estén disponibles, actualizar
 * modo_operacion a 'api' y rellenar url_servicio/token_servicio.
 */
class FaiConfigDemoSeeder extends Seeder
{
    public function run(): void
    {
        $sucursalId = DB::table('sucursal')->value('id');

        if (!$sucursalId) {
            $this->command->warn('FaiConfigDemoSeeder: no existe ninguna sucursal. Corre UniversidadDemoSeeder primero.');
            return;
        }

        $apifuente = DB::table('apifuente')->pluck('id', 'codigo');

        $configs = [
            ['fai_codigo' => 'RF02.1', 'apifuente_codigo' => 'DSA'],      // Créditos académicos
            ['fai_codigo' => 'RF02.2', 'apifuente_codigo' => 'DGA'],      // Voucher proyecto
            ['fai_codigo' => 'RF02.3', 'apifuente_codigo' => 'SUNEDU'],   // Grado de Bachiller
            ['fai_codigo' => 'RF02.4', 'apifuente_codigo' => 'DGA'],      // Voucher sustentación
            ['fai_codigo' => 'RF02.5', 'apifuente_codigo' => 'RENIEC'],   // Identidad DNI
            ['fai_codigo' => 'RF02.6', 'apifuente_codigo' => 'TURNITIN'], // Similitud Turnitin
        ];

        foreach ($configs as $cfg) {
            DB::table('faiconfig')->updateOrInsert(
                [
                    'sucursal_id' => $sucursalId,
                    'fai_codigo'  => $cfg['fai_codigo'],
                ],
                [
                    'apifuente_id'   => $apifuente[$cfg['apifuente_codigo']] ?? null,
                    'url_servicio'   => null,
                    'token_servicio' => null,
                    'activo'         => true,
                    'modo_operacion' => 'manual',
                    'updated_at'     => now(),
                    'created_at'     => now(),
                ]
            );
        }
    }
}
