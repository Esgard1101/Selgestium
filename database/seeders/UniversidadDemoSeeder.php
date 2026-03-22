<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UniversidadDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear Sucursal
        $sucursalId = \Illuminate\Support\Facades\DB::table('sucursal')->insertGetId([
            'descripcion' => 'Sede Central UNPRG',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Crear Usuario Administrador (Cesar)
        $userId = \Illuminate\Support\Facades\DB::table('users')->insertGetId([
            'name' => 'Cesar Admin',
            'email' => 'admin@selgestiun.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Asignar Rol Administrativo (id=9)
        \Illuminate\Support\Facades\DB::table('rolpersona')->insert([
            'usuario_id' => $userId,
            'rol_id' => 9,
            'sucursal_id' => $sucursalId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // 4. Crear Persona para Cesar
        \Illuminate\Support\Facades\DB::table('persona')->insert([
            'nombre' => 'Cesar',
            'apellido' => 'Admin',
            'dni' => '00000000',
            'email' => 'admin@selgestiun.com',
            'sucursal_id' => $sucursalId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
