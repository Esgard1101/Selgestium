<?php

namespace App\Services;

use App\Traits\DataBaseTrait;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PlazoService
{
    use DataBaseTrait;

    /**
     * Inicia un nuevo plazo para un expediente y fase (15 días hábiles).
     */
    public function iniciar(int $expedienteId, int $faseId): void
    {
        $fechaInicio = now();
        $fechaVencimiento = $fechaInicio->copy();
        
        $diasHabiles = 15;
        $diasAgregados = 0;

        while ($diasAgregados < $diasHabiles) {
            $fechaVencimiento->addDay();
            if (!$fechaVencimiento->isWeekend()) {
                $diasAgregados++;
            }
        }

        DB::table('controlplazo')->insert([
            'expediente_id' => $expedienteId,
            'fase_id' => $faseId,
            'fecha_inicio' => $fechaInicio,
            'fecha_vencimiento' => $fechaVencimiento,
            'dias_habiles' => $diasHabiles,
            'vencido' => false,
            'art123d_habilitado' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Job nocturno: actualiza vencido=true y encola alertas.
     */
    public function verificarVencidos(): void
    {
        $vencidos = DB::table('controlplazo')
            ->where('vencido', false)
            ->where('fecha_vencimiento', '<=', now())
            ->get();

        foreach ($vencidos as $plazo) {
            DB::table('controlplazo')
                ->where('id', $plazo->id)
                ->update([
                    'vencido' => true,
                    'updated_at' => now()
                ]);

            DB::table('alertaplazo')->insert([
                'controlplazo_id' => $plazo->id,
                'expediente_id' => $plazo->expediente_id,
                'destinatario_id' => null,
                'tipo_alerta' => 'vencimiento',
                'canal' => 'interno',
                'created_at' => now(),
            ]);
        }
    }

    /**
     * Verifica regla de 2/3 jurados aprobados + plazo vencido.
     */
    public function verificarArt123d(int $expedienteId): void
    {
        $plazo = DB::table('controlplazo')
            ->where('expediente_id', $expedienteId)
            ->where('vencido', true)
            ->first();

        if ($plazo) {
            $aprobados = DB::table('det_expedientejurado')
                ->where('expediente_id', $expedienteId)
                ->where('aprobado', true)
                ->count();

            if ($aprobados === 2) {
                DB::table('controlplazo')
                    ->where('id', $plazo->id)
                    ->update([
                        'art123d_habilitado' => true,
                        'updated_at' => now()
                    ]);

                DB::table('alertaplazo')->insert([
                    'controlplazo_id' => $plazo->id,
                    'expediente_id' => $expedienteId,
                    'destinatario_id' => null,
                    'tipo_alerta' => 'art123d',
                    'canal' => 'interno',
                    'created_at' => now(),
                ]);
            }
        }
    }
}
