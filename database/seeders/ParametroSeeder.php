<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParametroSeeder extends Seeder
{
    public function run(): void
    {
        $parametros = [
            ['codigo' => 'CREDITOS_MINIMOS',       'descripcion' => 'Creditos minimos para tesis',          'valor' => '160'],
            ['codigo' => 'TURNITIN_PROYECTO_MAX',   'descripcion' => 'Umbral similitud proyecto (%)',         'valor' => '30'],
            ['codigo' => 'TURNITIN_INFORME_MAX',    'descripcion' => 'Umbral similitud informe final (%)',    'valor' => '20'],
            ['codigo' => 'PLAZO_REVISION_JURADO',   'descripcion' => 'Dias para revision del jurado',         'valor' => '15'],
            ['codigo' => 'PLAZO_SUSTENTACION',      'descripcion' => 'Dias para programar sustentacion',      'valor' => '7'],
        ];

        foreach ($parametros as $p) {
            DB::table('parametro')->updateOrInsert(
                ['codigo' => $p['codigo']],
                [
                    'descripcion' => $p['descripcion'],
                    'valor'       => $p['valor'],
                    'sucursal_id' => null,
                    'updated_at'  => now(),
                    'created_at'  => now(),
                ]
            );
        }
    }
}
