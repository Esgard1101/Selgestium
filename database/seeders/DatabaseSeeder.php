<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Catálogos base (sin FK entre ellos)
            RolSeeder::class,
            FaseSeeder::class,
            ParametroSeeder::class,

            // Menú y permisos (opcionmenu antes que rol_opcionmenu)
            OpcionmenuSeeder::class,

            // Datos demo (sucursal + personas + usuarios + rolpersona)
            UniversidadDemoSeeder::class,

            // Cruces rol → opcion (requiere roles y opciones ya insertados)
            RolOpcionmenuSeeder::class,

            // FAI: config demo (requiere sucursal y apifuente ya insertados)
            FaiConfigDemoSeeder::class,
        ]);
    }
}
