<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OpcionmenuSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // ID 1 — visible para todos los roles
            [
                'id'          => 1,
                'ruta_nombre' => 'dashboard',
                'descripcion' => 'Panel Principal',
                'icono'       => 'fas fa-home',
                'grupo'       => 'General',
                'orden'       => 1,
                'activo'      => true,
            ],
            // ID 2 — Alumno: radicar su expediente
            [
                'id'          => 2,
                'ruta_nombre' => 'pur.create',
                'descripcion' => 'Radicar Expediente',
                'icono'       => 'fas fa-file-circle-plus',
                'grupo'       => 'Expediente',
                'orden'       => 1,
                'activo'      => true,
            ],
            // ID 3 — UI / Administrativo / Admin: asignar jurados
            [
                'id'          => 3,
                'ruta_nombre' => 'expediente.asignar-jurados.view',
                'descripcion' => 'Asignar Jurados',
                'icono'       => 'fas fa-user-check',
                'grupo'       => 'Expediente',
                'orden'       => 2,
                'activo'      => true,
            ],
            // ID 4 — Profesor / CC: registrar observaciones
            [
                'id'          => 4,
                'ruta_nombre' => 'expediente.observaciones.view',
                'descripcion' => 'Observaciones',
                'icono'       => 'fas fa-comments',
                'grupo'       => 'Expediente',
                'orden'       => 3,
                'activo'      => true,
            ],
            // ID 5 — UI / CC / Decanato / Admin: programar sustentacion
            [
                'id'          => 5,
                'ruta_nombre' => 'sustentacion.programar.show',
                'descripcion' => 'Programar Sustentacion',
                'icono'       => 'fas fa-calendar-check',
                'grupo'       => 'Sustentacion',
                'orden'       => 1,
                'activo'      => true,
            ],
            // ID 6 — UI / CC / Decanato / Admin: cerrar sustentacion
            [
                'id'          => 6,
                'ruta_nombre' => 'sustentacion.cerrar.show',
                'descripcion' => 'Cerrar Sustentacion',
                'icono'       => 'fas fa-flag-checkered',
                'grupo'       => 'Sustentacion',
                'orden'       => 2,
                'activo'      => true,
            ],
            // ID 7 — Admin / Administrativo: historial de acceso
            [
                'id'          => 7,
                'ruta_nombre' => 'bitacora.acceso.index',
                'descripcion' => 'Historial de Acceso',
                'icono'       => 'fas fa-shield-halved',
                'grupo'       => 'Seguridad',
                'orden'       => 1,
                'activo'      => true,
            ],
        ];

        foreach ($items as $item) {
            DB::table('opcionmenu')->updateOrInsert(
                ['id' => $item['id']],
                array_merge($item, ['updated_at' => now(), 'created_at' => now()])
            );
        }
    }
}
