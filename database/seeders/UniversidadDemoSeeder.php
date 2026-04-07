<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UniversidadDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Sucursal base
        $sucursalId = DB::table('sucursal')->insertGetId([
            'descripcion' => 'Sede Central UNPRG',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // ─── Usuarios de prueba por rol ───────────────────────────────────

        // Admin / Administrativo (rol_id = 9)
        $personaAdminId = DB::table('persona')->insertGetId([
            'nombre'      => 'Cesar',
            'apellido'    => 'Administrativo',
            'dni'         => '00000001',
            'email'       => 'admin@selgestium.com',
            'sucursal_id' => $sucursalId,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        $userAdminId = DB::table('users')->insertGetId([
            'name'        => 'Cesar Admin',
            'email'       => 'admin@selgestium.com',
            'password'    => Hash::make('password'),
            'persona_id'  => $personaAdminId,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        DB::table('rolpersona')->insert([
            'persona_id'  => $personaAdminId,
            'usuario_id'  => $userAdminId,
            'rol_id'      => 9,
            'sucursal_id' => $sucursalId,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // Alumno (rol_id = 6)
        $personaAlumnoId = DB::table('persona')->insertGetId([
            'nombre'      => 'Juan',
            'apellido'    => 'Alumno',
            'dni'         => '00000002',
            'email'       => 'alumno@selgestium.com',
            'sucursal_id' => $sucursalId,
            'creditos'    => 200,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        $userAlumnoId = DB::table('users')->insertGetId([
            'name'        => 'Juan Alumno',
            'email'       => 'alumno@selgestium.com',
            'password'    => Hash::make('password'),
            'persona_id'  => $personaAlumnoId,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        DB::table('rolpersona')->insert([
            'persona_id'  => $personaAlumnoId,
            'usuario_id'  => $userAlumnoId,
            'rol_id'      => 6,
            'sucursal_id' => $sucursalId,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // Unidad de Investigacion (rol_id = 10)
        $personaUiId = DB::table('persona')->insertGetId([
            'nombre'      => 'Maria',
            'apellido'    => 'Investigacion',
            'dni'         => '00000003',
            'email'       => 'ui@selgestium.com',
            'sucursal_id' => $sucursalId,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        $userUiId = DB::table('users')->insertGetId([
            'name'        => 'Maria UI',
            'email'       => 'ui@selgestium.com',
            'password'    => Hash::make('password'),
            'persona_id'  => $personaUiId,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        DB::table('rolpersona')->insert([
            'persona_id'  => $personaUiId,
            'usuario_id'  => $userUiId,
            'rol_id'      => 10,
            'sucursal_id' => $sucursalId,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // Comite Cientifico (rol_id = 11)
        $personaCcId = DB::table('persona')->insertGetId([
            'nombre'      => 'Pedro',
            'apellido'    => 'Cientifico',
            'dni'         => '00000004',
            'email'       => 'cc@selgestium.com',
            'sucursal_id' => $sucursalId,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        $userCcId = DB::table('users')->insertGetId([
            'name'        => 'Pedro CC',
            'email'       => 'cc@selgestium.com',
            'password'    => Hash::make('password'),
            'persona_id'  => $personaCcId,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        DB::table('rolpersona')->insert([
            'persona_id'  => $personaCcId,
            'usuario_id'  => $userCcId,
            'rol_id'      => 11,
            'sucursal_id' => $sucursalId,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }
}
