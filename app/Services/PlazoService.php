<?php

namespace App\Services;

use App\Traits\DataBaseTrait;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PlazoService
{
    use DataBaseTrait;

    /**
     * Inicia un nuevo plazo para un expediente y fase.
     */
    public function asignarPlazo(int $expedienteId, int $faseId, int $dias = 15): void
    {
        $fechaInicio = now();
        $fechaVencimiento = $fechaInicio->copy()->addDays($dias);

        $this->insertSingleDB('det_expedienteplazo', 0, [
            'expediente_id' => $expedienteId,
            'fase_id' => $faseId,
            'fecha_inicio' => $fechaInicio,
            'fecha_vencimiento' => $fechaVencimiento,
            'estado' => 'activo',
        ]);
    }

    /**
     * Verifica si el plazo de un expediente en su fase actual ha vencido.
     */
    public function verificarVencimiento(int $expedienteId): bool
    {
        $plazo = DB::table('det_expedienteplazo')
            ->where('expediente_id', $expedienteId)
            ->where('estado', 'activo')
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($plazo && Carbon::parse($plazo->fecha_vencimiento)->isPast()) {
            DB::table('det_expedienteplazo')
                ->where('id', $plazo->id)
                ->update(['estado' => 'vencido']);
            
            return true;
        }

        return false;
    }
}
