<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['id' => 6,  'descripcion' => 'Alumno'],
            ['id' => 7,  'descripcion' => 'Asesor Externo'],
            ['id' => 8,  'descripcion' => 'Profesor'],
            ['id' => 9,  'descripcion' => 'Administrativo'],
            ['id' => 10, 'descripcion' => 'Unidad de Investigacion'],
            ['id' => 11, 'descripcion' => 'Comite Cientifico'],
            ['id' => 12, 'descripcion' => 'Decanato'],
            ['id' => 13, 'descripcion' => 'Administrador'],
        ];

        foreach ($roles as $rol) {
            DB::table('rol')->updateOrInsert(
                ['id' => $rol['id']],
                ['descripcion' => $rol['descripcion'], 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
