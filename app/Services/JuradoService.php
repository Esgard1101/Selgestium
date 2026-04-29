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

    /**
     * Registra el veredicto de un jurado previa verificación 2FA.
     */
    public function registrarVeredicto(int $expedienteId, int $juradoId, bool $aprobado, string $codigo2fa, string $ip = null): void
    {
        $user = DB::table('users')->where('persona_id', $juradoId)->first();
        if (!$user) {
            throw new Exception("El jurado no posee usuario registrado para autenticación 2FA.");
        }

        $esValido = app(TwoFactorService::class)->verificarCodigo($user->id, $codigo2fa, 'firma_jurado');
        if (!$esValido) {
            throw new Exception("El código 2FA ingresado es incorrecto o ha caducado.");
        }

        DB::transaction(function () use ($expedienteId, $juradoId, $aprobado, $codigo2fa, $ip) {
            DB::table('det_expedientejurado')
                ->where('expediente_id', $expedienteId)
                ->where('jurado_id', $juradoId)
                ->update([
                    'aprobado' => $aprobado,
                    'fecha_evaluacion' => now(),
                    'updated_at' => now(),
                ]);

            DB::table('bit_firma')->insert([
                'expediente_id' => $expedienteId,
                'jurado_id' => $juradoId,
                'codigo_2fa' => $codigo2fa,
                'ip_origen' => $ip ?? request()->ip(),
                'fecha_firma' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->verificarAprobacionConsolidada($expedienteId);
        });
    }

    /**
     * Verifica si se cumple consenso para avanzar la fase.
     */
    public function verificarAprobacionConsolidada(int $expedienteId): void
    {
        $jurados = DB::table('det_expedientejurado')
            ->where('expediente_id', $expedienteId)
            ->where('activo', true)
            ->get();

        $aprobados = $jurados->where('aprobado', true)->count();
        $desaprobados = $jurados->whereNotNull('aprobado')->where('aprobado', false)->count();

        if ($aprobados === 3) {
            $presidente = $jurados->where('rol_jurado', 'presidente')->first();
            $actorId = $presidente ? $presidente->jurado_id : 0;

            app(ExpedienteService::class)->registrarCambioFase($expedienteId, 7, $actorId);

            DB::table('expediente')
                ->where('id', $expedienteId)
                ->update(['estado' => 'aprobado', 'updated_at' => now()]);
        } 
        elseif ($desaprobados > 1) {
            DB::table('expediente')
                ->where('id', $expedienteId)
                ->update(['estado' => 'en_subsanacion', 'updated_at' => now()]);

            $expediente = DB::table('expediente')->where('id', $expedienteId)->first();
            if ($expediente) {
                \Illuminate\Support\Facades\Log::info("Notificación enviada al estudiante {$expediente->estudiante_id}: Su expediente ha entrado en estado de subsanación.");
            }
        } 
        elseif ($aprobados === 2) {
            $plazo = DB::table('controlplazo')
                ->where('expediente_id', $expedienteId)
                ->where('vencido', true)
                ->first();

            if ($plazo) {
                app(PlazoService::class)->verificarArt123d($expedienteId);
            }
        }
    }
}
