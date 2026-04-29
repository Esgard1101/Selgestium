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
    public function programar(int $expedienteId, string $fechaHora, string $lugar, string $modalidad, ?int $resolucionId, int $actorId): void
    {
        $fechaSustentacion = Carbon::parse($fechaHora);

        // 1. Validar 7 días hábiles mínimos a partir de hoy
        $hoy = now();
        $diasHabiles = 0;
        $iter = $hoy->copy();

        while ($iter->lt($fechaSustentacion)) {
            $iter->addDay();
            if (!$iter->isWeekend()) {
                $diasHabiles++;
            }
        }

        if ($diasHabiles < 7) {
            throw new Exception("La fecha de sustentación debe ser al menos 7 días hábiles a partir de hoy (Art. 123).");
        }

        // 2. Validar no colisión de sala + hora
        $colision = DB::table('sustentacion')
            ->where('lugar', $lugar)
            ->where('fecha_hora', $fechaHora)
            ->whereNull('deleted_at')
            ->exists();

        if ($colision) {
            throw new Exception("Ya existe una sustentación programada en el mismo lugar y fecha/hora.");
        }

        // Obtener sucursal del expediente
        $expediente = DB::table('expediente')->where('id', $expedienteId)->first();
        if (!$expediente) {
            throw new Exception("El expediente no existe.");
        }

        // Insertar registro de sustentación
        DB::table('sustentacion')->updateOrInsert(
            ['expediente_id' => $expedienteId],
            [
                'sucursal_id' => $expediente->sucursal_id,
                'fecha_hora' => $fechaHora,
                'lugar' => $lugar,
                'modalidad' => $modalidad,
                'enlace_virtual' => request('enlace_virtual'),
                'resolucion_id' => $resolucionId,
                'estado' => 'programado',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Avanzar expediente a fase 10
        DB::table('expediente')
            ->where('id', $expedienteId)
            ->update([
                'fase_actual' => 10,
                'updated_at' => now()
            ]);
    }

    /**
     * Sustentaciones del mes.
     */
    public function calendario(?int $sucursalId, int $mes, int $anio): \Illuminate\Support\Collection
    {
        $query = DB::table('sustentacion')
            ->join('expediente', 'sustentacion.expediente_id', '=', 'expediente.id')
            ->leftJoin('persona as est', 'expediente.estudiante_id', '=', 'est.id')
            ->select(
                'sustentacion.*', 
                'expediente.titulo', 
                'expediente.numero_radicacion',
                DB::raw("CONCAT(est.nombre, ' ', est.apellido) as estudiante_nombre")
            )
            ->whereNull('sustentacion.deleted_at')
            ->whereMonth('sustentacion.fecha_hora', $mes)
            ->whereYear('sustentacion.fecha_hora', $anio);

        if ($sucursalId) {
            $query->where('sustentacion.sucursal_id', $sucursalId);
        }

        return $query->orderBy('sustentacion.fecha_hora', 'asc')->get();
    }

    /**
     * Registra el acta de sustentación y calcula promedio.
     */
    public function registrarActa(int $sustentacionId, array $notas, string $resultado, ?string $observaciones, int $actorId): void
    {
        DB::transaction(function () use ($sustentacionId, $notas, $resultado, $observaciones, $actorId) {
            $sustentacion = DB::table('sustentacion')->where('id', $sustentacionId)->first();
            if (!$sustentacion) {
                throw new Exception("La sustentación no existe.");
            }

            $nota1 = (float) ($notas[0] ?? 0);
            $nota2 = (float) ($notas[1] ?? 0);
            $nota3 = (float) ($notas[2] ?? 0);
            $promedio = round(($nota1 + $nota2 + $nota3) / 3, 2);

            DB::table('actasustentacion')->updateOrInsert(
                ['sustentacion_id' => $sustentacionId],
                [
                    'expediente_id' => $sustentacion->expediente_id,
                    'nota_jurado1' => $nota1,
                    'nota_jurado2' => $nota2,
                    'nota_jurado3' => $nota3,
                    'nota_promedio' => $promedio,
                    'resultado' => $resultado,
                    'observaciones_acta' => $observaciones,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            DB::table('sustentacion')
                ->where('id', $sustentacionId)
                ->update([
                    'nota_final' => $promedio,
                    'resultado' => $resultado,
                    'estado' => 'finalizado',
                    'updated_at' => now()
                ]);

            if (strtolower($resultado) === 'aprobado') {
                app(ExpedienteService::class)->cerrar($sustentacion->expediente_id, $actorId);
            }
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
            ->orderBy('sustentacion.fecha_hora', 'asc')
            ->get();
    }
}
