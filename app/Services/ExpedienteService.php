<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Services\NotificacionService;
use App\Traits\DataBaseTrait;
use App\Traits\LibraryTrait;
use App\Models\Expediente;
use Exception;

class ExpedienteService
{
    // Inyectamos los Traits obligatorios del equipo
    use DataBaseTrait, LibraryTrait;

    /**
     * Registra un nuevo expediente desde cero (RF-01)
     */
    public function registrarExpediente(array $datos, int $sucursalId, int $estudianteId, $archivoPdf)
    {
        //  Transacción para operaciones en múltiples tablas
        return DB::transaction(function () use ($datos, $sucursalId, $estudianteId, $archivoPdf) {
            try {
                // Generar número de radicación único 
                $numeroRadicacion = $this->generarNumeroRadicacion($sucursalId);

                //  Preparar el arreglo para la tabla 'expediente'
                $datosExpediente = [
                    'numero_radicacion' => $numeroRadicacion,
                    'estudiante_id'     => $estudianteId,
                    'asesor_id'         => $datos['asesor_id'] ?? null,
                    'sucursal_id'       => $sucursalId,
                    'titulo'            => $datos['titulo'],
                    'tipo'              => $datos['tipo'] ?? 'cuantitativo',
                    'etapa'             => 'I',         // I = Proyecto
                    'fase_actual'       => 1,           // 1 = Iniciar Proyecto
                    'estado'            => 'pendiente'
                ];

                //  Insertar usando el DataBaseTrait real del equipo
                // Usamos el parámetro 2 para "$table_id" (para la bitácora)
                // y devuelve un stdClass con el registro completo.
                // Como es una tabla nueva, no sabemos el $table_id de bitácora, le pasamos 0.
                $expedienteGuardado = $this->insertSingleDB('expediente', 0, $datosExpediente);
                $expedienteId = $expedienteGuardado->id;

                // Guardar documento PDF con trazabilidad de seguridad
                if ($archivoPdf) {
                    // Extraemos los datos de seguridad antes de mover el archivo
                    $nombreOriginal = $archivoPdf->getClientOriginalName();
                    $tamanio = $archivoPdf->getSize();
                    $hash = hash_file('sha256', $archivoPdf->getRealPath());

                    // Guardamos físicamente usando el Trait 
                    $rutaArchivo = $this->saveFile('expedientes/pdf', $archivoPdf);

                    //Preparamos el array con la nueva estructura de la tabla
                    $datosDocumento = [
                        'expediente_id'       => $expedienteId,
                        'tipo_documento'      => 'proyecto',
                        'nombre_original'     => $nombreOriginal,
                        'ruta_almacenamiento' => $rutaArchivo,
                        'tamanio_bytes'       => $tamanio,
                        'hash_sha256'         => $hash,
                        'subido_por_id'       => $estudianteId // Quien sube el archivo
                    ];

                    // Insertamos en la BD
                    $this->insertSingleDB('det_expedientedocumento', 0, $datosDocumento);
                }

                // Registrar la trazabilidad en el timeline
                $datosFase = [
                    'expediente_id' => $expedienteId,
                    'fase_id'       => 1, // 1 = Iniciar Proyecto
                    'actor_id'      => $estudianteId,
                    'comentario'    => 'Radicación inicial del expediente'
                ];
                $this->insertSingleDB('det_expedientefase', 0, $datosFase);
                $idUsuarioDestino = 5;

                $this->derivarExpediente(
                    $expedienteId,
                    $estudianteId,
                    $idUsuarioDestino,
                    'Derivación automática para revisión de requisitos iniciales.'
                );

                return $numeroRadicacion;
            } catch (Exception $e) {
                // Usamos el logError del LibraryTrait de tu equipo
                $this->logError($e);
                throw $e;
            }
        });
    }
    public function listarPorEstudiante(int $estudianteId, int $sucursalId)
    {
        // Regla: Paginación server-side nativa de Laravel, ordenado por fecha desc
        return \App\Models\Expediente::query()
            //->where('estudiante_id', $estudianteId)
            //->where('sucursal_id', $sucursalId)
            ->orderBy('created_at', 'desc')
            ->paginate(10); // ¡Laravel hace la magia de los offsets y limits aquí!
    }

    /**
     * Genera el correlativo único con formato EXP-{YYYY}-{SUCURSAL_ID}-{SEQUENCE}
     */
    private function generarNumeroRadicacion(int $sucursalId): string
    {
        $anio = date('Y');
        $prefijo = "EXP-{$anio}-{$sucursalId}-";

        $ultimoExpediente = Expediente::where('sucursal_id', $sucursalId)
            ->where('numero_radicacion', 'like', "{$prefijo}%")
            ->lockForUpdate()
            ->orderBy('id', 'desc')
            ->first();

        $secuencia = 1;
        if ($ultimoExpediente) {
            $partes = explode('-', $ultimoExpediente->numero_radicacion);
            $secuencia = (int) end($partes) + 1;
        }

        $secuenciaFormateada = str_pad((string)$secuencia, 5, '0', STR_PAD_LEFT);

        return $prefijo . $secuenciaFormateada;
    }

    public function derivarExpediente(int $expedienteId, int $actorOrigenId, int $actorDestinoId, string $comentario)
    {
        return DB::transaction(function () use ($expedienteId, $actorOrigenId, $actorDestinoId, $comentario) {
            try {
                //  Registrar el movimiento en el timeline
                $datosFase = [
                    'expediente_id' => $expedienteId,
                    'fase_id'       => 1,
                    'actor_id'      => $actorOrigenId,
                    'comentario'    => "Derivado al usuario ID {$actorDestinoId} - " . $comentario
                ];
                $this->insertSingleDB('det_expedientefase', 0, $datosFase);

                // Supongamos que tienen 15 días para esta revisión inicial
                $datosPlazo = [
                    'expediente_id'     => $expedienteId,
                    'fase_id'           => 1,
                    'fecha_inicio'      => now(),
                    'fecha_vencimiento' => now()->addDays(15), // Ajusta los días según tu regla de negocio
                    'estado'            => 'activo',
                    'created_at'        => now()
                ];
                // Usamos insertSingleDB y capturamos el objeto devuelto para sacar su ID
                $plazoGuardado = $this->insertSingleDB('det_expedienteplazo', 0, $datosPlazo);

                //  Generar la Alerta enlazada al plazo que acabamos de crear
                $datosNotificacion = [
                    'expediente_id'  => $expedienteId,
                    'plazo_id'       => $plazoGuardado->id, // 
                    'tipo'           => 'Derivación Automática',
                    'mensaje'        => "Expediente derivado para revisión inicial.",
                    'enviado_comite' => false,
                    'fecha_alerta'   => now(),
                    'created_at'     => now()
                ];

                $this->insertSingleDB('det_expedientealerta', 0, $datosNotificacion);

                return true;
            } catch (Exception $e) {
                $this->logError($e);
                throw $e;
            }
        });
    }
    /**
     * Deriva el expediente a una nueva fase y genera la notificación correspondiente.
     */
    public function derivar(int $expedienteId, int $nuevaFaseId, int $actorId, string $ip)
    {
        DB::transaction(function () use ($expedienteId, $nuevaFaseId, $actorId, $ip) {
            // Actualizar la fase actual en el expediente
            DB::table('expediente')->where('id', $expedienteId)->update([
                'fase_actual' => $nuevaFaseId,
                'updated_at' => now()
            ]);

            //Registrar el movimiento en el historial (det_expedientefase)
            DB::table('det_expedientefase')->insert([
                'expediente_id' => $expedienteId,
                'fase_id' => $nuevaFaseId,
                'actor_id' => $actorId,
                'ip_actor' => $ip,
                'fecha_inicio' => now(),
                'created_at' => now()
            ]);

            // Notificación Automática 
            // Si es Fase 1 (Radicación), avisamos a la UI
            if ($nuevaFaseId == 1) {
                $rolDestino = match (true) {
                    $nuevaFaseId <= 3 => 'ui',
                    $nuevaFaseId == 4 => 'cc',
                    default           => 'jurado'
                };

                // Buscamos a las personas que tengan ese rol para notificarles
                $destinatarios = DB::table('rolpersona')
                    ->where('rol_id', $rolDestino)
                    ->pluck('persona_id')
                    ->toArray();

                // Opcional: agregamos al actor (estudiante) para que también reciba copia
                $destinatarios[] = $actorId;

                // Encolamos (asegurando que no haya IDs duplicados)
                $codigoPlantilla = $nuevaFaseId == 1 ? 'EXP_RADICADO' : 'EXP_DERIVADO';

                if (!empty($destinatarios)) {
                    NotificacionService::encolar($expedienteId, $codigoPlantilla, array_unique($destinatarios));
                }
            }
        });
    }
    /**
     * Cierra el expediente de forma definitiva.
     */
    public function cerrar(int $expedienteId, int $actorId): void
    {
        DB::transaction(function () use ($expedienteId, $actorId) {
            DB::table('expediente')
                ->where('id', $expedienteId)
                ->update([
                    'estado' => 'cerrado',
                    'updated_at' => now(),
                ]);

            DB::table('bit_expediente')->insert([
                'expediente_id' => $expedienteId,
                'actor_id' => $actorId,
                'accion' => 'cierre_expediente',
                'ip' => request()->ip(),
                'created_at' => now(),
            ]);
        });
    }
}
