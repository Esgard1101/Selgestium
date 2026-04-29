<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlantillaNotificacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('plantillanotificacion')->insert([
            'codigo' => 'EXP_RADICADO',
            'canal' => 'sistema',
            'asunto' => 'Nuevo Expediente Radicado',
            'cuerpo' => 'Se ha radicado un nuevo expediente y requiere su revisión en la Unidad de Investigación.',
            'activo' => true,
            'created_at' => now(),
        ]);
    }
}
