<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * FaiService — Panel unificado FAI (F-17)
 *
 * Lectura y agregación de resultados para la bandeja de verificaciones
 * pendientes y el panel semafórico por expediente.
 */
class FaiService
{
    /** FAIs que aplican por fase */
    private const FAI_POR_FASE = [
        2 => ['RF02.1', 'RF02.2', 'RF02.5'],
        8 => ['RF02.3', 'RF02.4', 'RF02.6'],
    ];

    /**
     * Expedientes en fase 2 u 8 con al menos 1 FAI sin aprobar.
     *
     * Evita N+1 cargando todos los fairesultado aprobados del sucursal
     * en una sola consulta y filtrando en PHP.
     *
     * @return Collection  Cada item tiene los campos del expediente
     *                     más `faltantes` (array de fai_codigo sin aprobar).
     */
    public function verificacionesPendientes(int $sucursal_id): Collection
    {
        // 1. Expedientes en fase 2 u 8 de la sucursal
        $expedientes = DB::table('expediente as e')
            ->join('persona as p', 'p.id', '=', 'e.estudiante_id')
            ->select(
                'e.id',
                'e.numero_radicacion',
                'e.titulo',
                'e.fase_actual',
                DB::raw("CONCAT(p.nombre, ' ', p.apellido) AS estudiante")
            )
            ->where('e.sucursal_id', $sucursal_id)
            ->whereIn('e.fase_actual', [2, 8])
            ->whereNull('e.deleted_at')
            ->orderByDesc('e.created_at')
            ->get();

        if ($expedientes->isEmpty()) {
            return collect();
        }

        // 2. FAIs aprobados para todos esos expedientes (una sola query)
        $ids = $expedientes->pluck('id');
        $aprobados = DB::table('fairesultado')
            ->select('expediente_id', 'fai_codigo')
            ->whereIn('expediente_id', $ids)
            ->where('estado', 'aprobado')
            ->get()
            ->groupBy('expediente_id')
            ->map(fn($grupo) => $grupo->pluck('fai_codigo')->all());

        // 3. Calcular faltantes y filtrar expedientes que tengan al menos 1
        return $expedientes
            ->map(function ($exp) use ($aprobados) {
                $fase    = (int) $exp->fase_actual;
                $requeridos = self::FAI_POR_FASE[$fase] ?? [];
                $aprobadosExp = $aprobados->get($exp->id, []);
                $faltantes = array_values(
                    array_diff($requeridos, $aprobadosExp)
                );

                $exp->faltantes = $faltantes;
                return $exp;
            })
            ->filter(fn($exp) => count($exp->faltantes) > 0)
            ->values();
    }

    /**
     * Último resultado de cada uno de los 6 FAI codes para un expediente.
     *
     * @return Collection  6 items: {fai_codigo, resultado|null}
     */
    public function resultadosParaExpediente(int $expediente_id): Collection
    {
        $allCodes = ['RF02.1', 'RF02.2', 'RF02.3', 'RF02.4', 'RF02.5', 'RF02.6'];

        $resultados = DB::table('fairesultado as fr')
            ->leftJoin('persona as p', 'p.id', '=', 'fr.validado_por_id')
            ->select(
                'fr.fai_codigo',
                'fr.estado',
                'fr.valor_obtenido',
                'fr.valor_umbral',
                'fr.verificado_at',
                'fr.motivorechazo_id',
                DB::raw("CONCAT(p.nombre, ' ', p.apellido) AS validado_por")
            )
            ->where('fr.expediente_id', $expediente_id)
            ->orderByDesc('fr.created_at')
            ->get()
            ->groupBy('fai_codigo')
            ->map(fn($grupo) => $grupo->first());

        return collect($allCodes)->map(fn($code) => (object)[
            'fai_codigo' => $code,
            'resultado'  => $resultados->get($code),
        ]);
    }
}
