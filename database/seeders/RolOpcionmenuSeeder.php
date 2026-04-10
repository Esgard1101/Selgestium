<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolOpcionmenuSeeder extends Seeder
{
    /**
     * Mapeo de roles a opciones de menú.
     *
     * Roles: 6=Alumno, 7=AsesorExterno, 8=Profesor, 9=Administrativo,
     *        10=UI, 11=CC, 12=Decanato, 13=Admin
     *
     * Opciones: 1=Dashboard, 2=RadicarExpediente, 3=AsignarJurados,
     *           4=Observaciones, 5=ProgramarSustentacion,
     *           6=CerrarSustentacion, 7=HistorialAcceso, 8=PanelFAI
     */
    public function run(): void
    {
        $mapeo = [
            6  => [1, 2],              // Alumno
            7  => [1],                 // Asesor Externo
            8  => [1, 4],              // Profesor / Jurado
            9  => [1, 3, 7],           // Administrativo
            10 => [1, 3, 5, 6, 8],     // Unidad de Investigacion
            11 => [1, 3, 4, 5, 6, 8],  // Comite Cientifico
            12 => [1, 5, 6],           // Decanato
            13 => [1, 2, 3, 4, 5, 6, 7, 8], // Admin (acceso total)
        ];

        foreach ($mapeo as $rolId => $opciones) {
            foreach ($opciones as $opcionId) {
                DB::table('rol_opcionmenu')->updateOrInsert(
                    ['rol_id' => $rolId, 'opcionmenu_id' => $opcionId],
                    ['rol_id' => $rolId, 'opcionmenu_id' => $opcionId, 'created_at' => now()]
                );
            }
        }
    }
}
