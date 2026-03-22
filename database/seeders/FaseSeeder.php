<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fases = [
            1 => 'Iniciar Proyecto',
            2 => 'Verificar Requisitos',
            3 => 'Verificar Duplicidad',
            4 => 'Presentacion de Proyecto',
            5 => 'Asignar Jurado',
            6 => 'Revision del Proyecto',
            7 => 'Aprobacion del Proyecto',
            8 => 'Presentacion Informe Final',
            9 => 'Revision del Informe',
            10 => 'Programar Sustentacion',
            11 => 'Sustentacion',
        ];

        foreach ($fases as $id => $descripcion) {
            \Illuminate\Support\Facades\DB::table('fase')->updateOrInsert(['id' => $id], [
                'descripcion' => $descripcion,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
