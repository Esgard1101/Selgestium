<?php

namespace App\Services;

use App\Traits\DataBaseTrait;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class SustentacionService
{
    use DataBaseTrait;

    /**
     * Valida y programa una sustentación.
     */
    public function programar(array $data): void
    {
        $expedienteId = $data['expediente_id'];
        $fechaStr = $data['fecha'];

        // Validar que el expediente esté aprobado (Fase 7)
        $expediente = DB::table('expediente')->where('id', $expedienteId)->first();
        if (!$expediente || $expediente->fase_actual < 7) {
            throw new Exception("El expediente debe estar aprobado (Fase 7) para programar la sustentación.");
        }

        // Validar 7 días hábiles mínimos
        if (!$this->esFechaValida($fechaStr)) {
            throw new Exception("La fecha de sustentación debe ser al menos 7 días hábiles a partir de hoy (Art. 123).");
        }

        $this->insertSingleDB('sustentacion', 0, [
            'expediente_id' => $expedienteId,
            'fecha' => $fechaStr,
            'hora' => $data['hora'],
            'lugar' => $data['lugar'],
            'estado' => 'programado',
        ]);

        // Avanzar a Fase 10 (Programar Sustentacion completada) 
        // Aunque la fase 10 del seeder es "Programar", lo movemos a 11 si ya se programó?
        // Vamos a moverlo a la fase 10.
        DB::table('expediente')->where('id', $expedienteId)->update([
            'fase_actual' => 10,
            'updated_at' => now(),
        ]);
    }

    /**
     * Verifica si una fecha cumple con el mínimo de 7 días hábiles.
     */
    protected function esFechaValida(string $fechaStr): bool
    {
        $fechaSustentacion = Carbon::parse($fechaStr);
        $hoy = now();
        
        $diasHabiles = 0;
        $iter = $hoy->copy();

        while ($iter->lt($fechaSustentacion)) {
            $iter->addDay();
            if (!$iter->isWeekend()) {
                $diasHabiles++;
            }
        }

        return $diasHabiles >= 7;
    }

    /**
     * Registra el acta final y cierra el expediente (Feature 8).
     */
    public function cerrarExpediente(array $data): void
    {
        $expedienteId = $data['expediente_id'];

        DB::transaction(function () use ($data, $expedienteId) {
            // Guardar Acta usando DataBaseTrait
            $this->insertSingleDB('det_expedienteacta', 0, [
                'expediente_id' => $expedienteId,
                'numero_acta' => $data['numero_acta'],
                'fecha_sustentacion' => $data['fecha_sustentacion'],
                'resultado' => $data['resultado'],
                'observaciones' => $data['observaciones'] ?? '',
            ]);

            // Cerrar Expediente
            DB::table('expediente')->where('id', $expedienteId)->update([
                'estado' => 'cerrado',
                'fase_actual' => 11, // Fase Final: Sustentación/Cierre
                'updated_at' => now(),
            ]);
        });
    }

    /**
     * Obtener sustentaciones para el dashboard.
     */
    public function obtenerProgramadas(): \Illuminate\Support\Collection
    {
        return DB::table('sustentacion')
            ->join('expediente', 'sustentacion.expediente_id', '=', 'expediente.id')
            ->select('sustentacion.*', 'expediente.titulo', 'expediente.numero_radicacion')
            ->where('sustentacion.estado', 'programado')
            ->whereNull('sustentacion.deleted_at')
            ->orderBy('sustentacion.fecha', 'asc')
            ->get();
    }
}
