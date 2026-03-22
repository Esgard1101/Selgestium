<?php

namespace App\Services;

use App\Traits\DataBaseTrait;
use Illuminate\Support\Facades\DB;
use Exception;

class ObservacionService
{
    use DataBaseTrait;

    /**
     * Registrar una observación de un jurado.
     *
     * @param array $data ['expediente_id', 'jurado_id', 'ronda', 'descripcion', 'tipo_veredicto']
     * @throws Exception
     */
    public function registrarObservacion(array $data): void
    {
        $expedienteId = $data['expediente_id'];
        $juradoId = $data['jurado_id'];
        $ronda = $data['ronda'] ?? 1;

        // REGLA CRÍTICA RGT: Bloqueo de nuevas observaciones en ronda > 1
        if ($ronda > 1) {
            $ultimaObservacion = DB::table('det_expedienteobservacion')
                ->where('expediente_id', $expedienteId)
                ->where('jurado_id', $juradoId)
                ->where('ronda', 1)
                ->first();

            if ($ultimaObservacion && $ultimaObservacion->bloqueado) {
                throw new Exception("Bloqueo RGT: No se permiten nuevas observaciones tras la primera ronda para este jurado.");
            }
        }

        DB::transaction(function () use ($data, $expedienteId, $juradoId, $ronda) {
            // Guardar observación usando DataBaseTrait
            $this->insertSingleDB('det_expedienteobservacion', 0, [
                'expediente_id' => $expedienteId,
                'jurado_id' => $juradoId,
                'ronda' => $ronda,
                'descripcion' => $data['descripcion'],
                'tipo_veredicto' => $data['tipo_veredicto'] ?? 'observado',
                'bloqueado' => ($ronda == 1), // La ronda 1 bloquea futuras rondas
            ]);

            // Actualizar estado del expediente según aprobaciones
            $this->actualizarEstadoExpediente($expedienteId, $ronda);
        });
    }

    /**
     * Actualiza el estado y fase del expediente según el consenso de los 3 jurados.
     */
    protected function actualizarEstadoExpediente(int $expedienteId, int $ronda): void
    {
        $expediente = DB::table('expediente')->where('id', $expedienteId)->first();

        // Obtener observaciones de la ronda actual
        $observaciones = DB::table('det_expedienteobservacion')
            ->where('expediente_id', $expedienteId)
            ->where('ronda', $ronda)
            ->get();

        // Necesitamos que los 3 jurados hayan comentado para cambiar estado global
        if ($observaciones->count() < 3) {
            return;
        }

        $aprobados = $observaciones->where('tipo_veredicto', 'aprobado')->count();

        if ($aprobados === 3) {
            // Aprobación consolidada -> Avanzar a Fase 7 (Aprobacion del Proyecto)
            DB::table('expediente')->where('id', $expedienteId)->update([
                'fase_actual' => 7,
                'estado' => 'aprobado',
                'updated_at' => now(),
            ]);
        } else {
            // Si hay al menos un "observado" entre los 3 votos -> En subsanación
            DB::table('expediente')->where('id', $expedienteId)->update([
                'estado' => 'en_subsanacion',
                'updated_at' => now(),
            ]);
        }
    }
}
