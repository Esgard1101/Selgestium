<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
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

                // Guardar documento PDF
                if ($archivoPdf) {
                    // El LibraryTrait de tu equipo pide subfolder y el archivo
                    $rutaArchivo = $this->saveFile('expedientes/pdf', $archivoPdf);

                    $datosDocumento = [
                        'expediente_id' => $expedienteId,
                        'ruta_archivo'  => $rutaArchivo,
                        'tipo_documento' => 'proyecto'
                    ];
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

                return $numeroRadicacion;
            } catch (Exception $e) {
                // Usamos el logError del LibraryTrait de tu equipo
                $this->logError($e);
                throw $e;
            }
        });
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
}
