<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PlazoController extends Controller
{
    /**
     * Muestra el tablero de Control de Plazos.
     */
    public function dashboard()
    {
        $plazos = DB::table('controlplazo as cp')
            ->join('expediente as e', 'e.id', '=', 'cp.expediente_id')
            ->join('persona as p', 'p.id', '=', 'e.estudiante_id')
            ->select(
                'cp.*',
                'e.numero_radicacion',
                'e.titulo',
                DB::raw("CONCAT(p.nombre, ' ', p.apellido) AS estudiante")
            )
            ->orderByDesc('cp.fecha_vencimiento')
            ->get();

        $vencidosHoy = 0;
        $porVencer3Dias = 0;
        $art123dHabilitados = 0;

        $ahora = now();

        foreach ($plazos as $plazo) {
            $fechaVenc = Carbon::parse($plazo->fecha_vencimiento);
            
            // 1. Vencidos hoy
            if ($plazo->vencido || $fechaVenc->isPast()) {
                if ($fechaVenc->isToday()) {
                    $vencidosHoy++;
                }
            }

            // 2. Por vencer en 3 días
            if (!$plazo->vencido && !$fechaVenc->isPast()) {
                $diasHabilesRestantes = $this->calcularDiasHabilesRestantes($ahora, $fechaVenc);
                $plazo->dias_restantes = $diasHabilesRestantes;
                
                if ($diasHabilesRestantes <= 3 && $diasHabilesRestantes > 0) {
                    $porVencer3Dias++;
                }
            } else {
                $plazo->dias_restantes = 0;
            }

            // 3. Art. 123-d
            if ($plazo->art123d_habilitado) {
                $art123dHabilitados++;
            }
        }

        return view('plazo.dashboard', compact(
            'plazos',
            'vencidosHoy',
            'porVencer3Dias',
            'art123dHabilitados'
        ));
    }

    /**
     * Calcula los días hábiles restantes entre dos fechas excluyendo fines de semana.
     */
    private function calcularDiasHabilesRestantes(Carbon $desde, Carbon $hasta): int
    {
        if ($desde->gt($hasta)) {
            return 0;
        }

        $dias = 0;
        $actual = $desde->copy();

        while ($actual->lt($hasta)) {
            $actual->addDay();
            if (!$actual->isWeekend()) {
                $dias++;
            }
        }

        return $dias;
    }
}
