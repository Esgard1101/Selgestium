<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Limpiamos la tabla para evitar duplicados si corres el seeder varias veces
        User::truncate();

        // Crear el Usuario Administrador Líder
        User::create([
            'name' => 'Admin FACHSE',
            'email' => 'admin@selgestiun.edu.pe',
            'password' => Hash::make('Admin1234!'), // Contraseña segura y hasheada
            'email_verified_at' => now(),
            // 'sucursal_id' => 1 // Esto lo activaremos cuando liguemos la tabla sucursal
        ]);

        // Aquí luego agregaremos:
        // $this->call(RolSeeder::class);
        // $this->call(ParametroSeeder::class);
    }
}