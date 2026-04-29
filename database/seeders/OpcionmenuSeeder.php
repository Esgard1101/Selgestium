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
            // ID 3 — UI / Administrativo / Admin / CC: asignar jurados
            [
                'id'          => 3,
                'ruta_nombre' => 'jurado.asignar',
                'descripcion' => 'Asignar Jurado',
                'icono'       => 'fas fa-user-check',
                'grupo'       => 'Jurado',
                'orden'       => 2,
                'activo'      => true,
            ],
            // ID 4 — Profesor / CC: registrar observaciones
            [
                'id'          => 4,
                'ruta_nombre' => 'jurado.mis_revisiones',
                'descripcion' => 'Mis Revisiones',
                'icono'       => 'fas fa-comments',
                'grupo'       => 'Jurado',
                'orden'       => 3,
                'activo'      => true,
            ],
            // ID 5 — UI / CC / Decanato / Admin: programar sustentacion
            [
                'id'          => 5,
                'ruta_nombre' => 'sustentacion.programar.index',
                'descripcion' => 'Programar Sustentacion',
                'icono'       => 'fas fa-calendar-check',
                'grupo'       => 'Sustentacion',
                'orden'       => 1,
                'activo'      => true,
            ],
            // ID 6 — UI / CC / Decanato / Admin: cerrar sustentacion
            [
                'id'          => 6,
                'ruta_nombre' => 'sustentacion.cerrar.index',
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
            // ID 8 — UI / CC / Admin: panel FAI unificado (F-17)
            [
                'id'          => 8,
                'ruta_nombre' => 'fai.panel.index',
                'descripcion' => 'Panel FAI',
                'icono'       => 'fas fa-filter',
                'grupo'       => 'FAI',
                'orden'       => 1,
                'activo'      => true,
            ],
            // ID 9 — FAI Etapa II
            [
                'id'          => 9,
                'ruta_nombre' => 'fai.etapa2.index',
                'descripcion' => 'FAI Etapa II',
                'icono'       => 'fas fa-graduation-cap',
                'grupo'       => 'FAI',
                'orden'       => 2,
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
