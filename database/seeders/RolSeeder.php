<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['id' => 6, 'descripcion' => 'Alumno'],
            ['id' => 7, 'descripcion' => 'Asesor Externo'],
            ['id' => 8, 'descripcion' => 'Profesor'],
            ['id' => 9, 'descripcion' => 'Administrativo'],
            ['id' => 10, 'descripcion' => 'Unidad de Investigacion'],
            ['id' => 11, 'descripcion' => 'Comite Cientifico'],
            ['id' => 12, 'descripcion' => 'Decanato'],
        ];

        foreach ($roles as $rol) {
            \Illuminate\Support\Facades\DB::table('rol')->updateOrInsert(['id' => $rol['id']], [
                'descripcion' => $rol['descripcion'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
