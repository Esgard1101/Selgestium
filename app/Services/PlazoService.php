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
            
            $this->registrarAlerta($plazo);
            
            return true;
        }

        return false;
    }

    /**
     * Registra una alerta formal y simula notificación al comité (Art. 123-d).
     */
    protected function registrarAlerta(object $plazo): void
    {
        $existe = DB::table('det_expedientealerta')
            ->where('plazo_id', $plazo->id)
            ->exists();

        if (!$existe) {
            $this->insertSingleDB('det_expedientealerta', 0, [
                'expediente_id' => $plazo->expediente_id,
                'plazo_id' => $plazo->id,
                'tipo' => 'vencimiento_15_dias',
                'mensaje' => "El expediente con ID {$plazo->expediente_id} ha superado el plazo de 15 días en la fase de revisión (Art. 123-d).",
                'enviado_comite' => true,
                'fecha_alerta' => now(),
            ]);

            \Illuminate\Support\Facades\Log::info("Notificación encolada al Comité de Ética (Art. 123-d) para expediente {$plazo->expediente_id}");
        }
    }
}
