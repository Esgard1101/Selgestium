<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UniversidadDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $sucursalId = DB::table('sucursal')->insertGetId([
                'descripcion' => 'Sede Central UNPRG',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            // Contraseña uniforme de desarrollo: password
            $usuarios = [
                ['nombre' => 'Cesar',  'apellido' => 'Administrativo', 'dni' => '00000001', 'email' => 'admin@selgestium.com',      'rol_id' => 9,  'creditos' => null],
                ['nombre' => 'Juan',   'apellido' => 'Alumno',         'dni' => '00000002', 'email' => 'alumno@selgestium.com',     'rol_id' => 6,  'creditos' => 200],
                ['nombre' => 'Maria',  'apellido' => 'Investigacion',  'dni' => '00000003', 'email' => 'ui@selgestium.com',         'rol_id' => 10, 'creditos' => null],
                ['nombre' => 'Pedro',  'apellido' => 'Cientifico',     'dni' => '00000004', 'email' => 'cc@selgestium.com',         'rol_id' => 11, 'creditos' => null],
                ['nombre' => 'Rosa',   'apellido' => 'Asesor',         'dni' => '00000007', 'email' => 'asesor@selgestium.com',     'rol_id' => 7,  'creditos' => null],
                ['nombre' => 'Luis',   'apellido' => 'Profesor',       'dni' => '00000008', 'email' => 'profesor@selgestium.com',   'rol_id' => 8,  'creditos' => null],
                ['nombre' => 'Ana',    'apellido' => 'Decana',         'dni' => '00000012', 'email' => 'decano@selgestium.com',     'rol_id' => 12, 'creditos' => null],
                ['nombre' => 'Carlos', 'apellido' => 'Administrador',  'dni' => '00000013', 'email' => 'superadmin@selgestium.com', 'rol_id' => 13, 'creditos' => null],
            ];

            foreach ($usuarios as $u) {
                $personaData = [
                    'nombre'      => $u['nombre'],
                    'apellido'    => $u['apellido'],
                    'dni'         => $u['dni'],
                    'email'       => $u['email'],
                    'sucursal_id' => $sucursalId,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
                if (!is_null($u['creditos'])) {
                    $personaData['creditos'] = $u['creditos'];
                }

                $personaId = DB::table('persona')->insertGetId($personaData);

                $userId = DB::table('users')->insertGetId([
                    'name'              => $u['nombre'] . ' ' . $u['apellido'],
                    'email'             => $u['email'],
                    'password'          => Hash::make('password'),
                    'persona_id'        => $personaId,
                    // email_verified_at permite acceder sin verificacion en desarrollo
                    'email_verified_at' => now(),
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);

                DB::table('rolpersona')->insert([
                    'persona_id'  => $personaId,
                    'usuario_id'  => $userId,
                    'rol_id'      => $u['rol_id'],
                    'sucursal_id' => $sucursalId,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        });
    }
}
