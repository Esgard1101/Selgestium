<?php

namespace App\Services;

use App\Traits\DataBaseTrait;
use Illuminate\Support\Facades\DB;
use Exception;

class JuradoService
{
    use DataBaseTrait;

    /**
     * Asignar jurados a un expediente.
     *
     * @param int $expedienteId
     * @param array $jurados Associative array mapping role to jurado_id: ['presidente' => id, 'secretario' => id, 'vocal' => id]
     * @param int $actorId
     * @throws Exception
     */
    public function asignar(int $expedienteId, array $jurados, int $actorId): void
    {
        if (count($jurados) !== 3) {
            throw new Exception("Se deben asignar exactamente 3 jurados.");
        }

        // Check for duplicate IDs
        if (count(array_unique($jurados)) !== 3) {
            throw new Exception("Los jurados deben ser personas distintas.");
        }

        DB::transaction(function () use ($expedienteId, $jurados, $actorId) {
            $expediente = DB::table('expediente')->where('id', $expedienteId)->first();
            if (!$expediente) {
                throw new Exception("El expediente no existe.");
            }

            if ($expediente->estado === 'cerrado') {
                throw new Exception("El expediente está CERRADO y no permite más asignaciones.");
            }

            // El docente asesor no puede ser jurado
            if (in_array($expediente->asesor_id, $jurados)) {
                throw new Exception("El docente que es asesor del expediente no puede ser asignado como jurado.");
            }

            // Obtener la resolución más reciente para este expediente
            $resolucion = DB::table('resoluciones')
                ->where('expediente_id', $expedienteId)
                ->orderBy('created_at', 'desc')
                ->first();

            $resolucionId = $resolucion ? $resolucion->id : null;

            // Desactivar jurados anteriores si los hay
            DB::table('det_expedientejurado')
                ->where('expediente_id', $expedienteId)
                ->update(['activo' => false]);

            foreach ($jurados as $rol => $juradoId) {
                // Verificar que la persona existe y tiene el rol de Profesor/Jurado (ID 8)
                $persona = DB::table('persona')->where('id', $juradoId)->first();
                if (!$persona) {
                    throw new Exception("El jurado con ID {$juradoId} no existe.");
                }

                $esJurado = DB::table('rolpersona')
                    ->where('persona_id', $juradoId)
                    ->where('rol_id', 8)
                    ->exists();

                if (!$esJurado) {
                    throw new Exception("La persona con ID {$juradoId} no tiene el rol de Jurado.");
                }

                DB::table('det_expedientejurado')->insert([
                    'expediente_id' => $expedienteId,
                    'jurado_id' => $juradoId,
                    'rol_jurado' => $rol,
                    'fecha_asignacion' => now(),
                    'resolucion_id' => $resolucionId,
                    'activo' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }

    /**
     * Registrar una resolución para un expediente.
     *
     * @param int $expedienteId
     * @param string $numero
     * @param string $fecha
     * @param int $emitidoPorId
     * @return int ID de la resolución creada
     * @throws Exception
     */
    public function registrarResolucion(int $expedienteId, string $numero, string $fecha, int $emitidoPorId): int
    {
        $expediente = DB::table('expediente')->where('id', $expedienteId)->first();
        if (!$expediente) {
            throw new Exception("El expediente no existe.");
        }

        return DB::table('resoluciones')->insertGetId([
            'expediente_id' => $expedienteId,
            'sucursal_id' => $expediente->sucursal_id,
            'numero_resolucion' => $numero,
            'fecha_emision' => $fecha,
            'emitido_por_id' => $emitidoPorId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
