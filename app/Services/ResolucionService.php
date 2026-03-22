<?php

namespace App\Services;

use App\Models\Expediente;
use Illuminate\Support\Facades\DB;

class ResolucionService
{
    /**
     * Obtiene todos los datos necesarios para imprimir la resolución de designación.
     */
    public function obtenerDatosResolucion(int $expedienteId): array
    {
        // Obtener el expediente con su estudiante y sucursal
        $expediente = Expediente::with(['estudiante', 'sucursal'])->findOrFail($expedienteId);
        
        // Obtener los 3 jurados asignados
        $jurados = DB::table('det_expedientejurado')
            ->join('persona', 'det_expedientejurado.jurado_id', '=', 'persona.id')
            ->where('expediente_id', $expedienteId)
            ->whereNull('det_expedientejurado.deleted_at')
            ->select('persona.nombre', 'persona.apellido', 'persona.dni')
            ->get();

        return [
            'numero_resolucion' => 'RES-FD-' . date('Y') . '-' . str_pad($expedienteId, 5, '0', STR_PAD_LEFT),
            'fecha_emision' => now()->locale('es')->isoFormat('LL'),
            'expediente' => $expediente,
            'estudiante' => $expediente->estudiante,
            'jurados' => $jurados,
            'universidad' => config('app.university_name', 'UNPRG'),
            'facultad' => config('app.faculty_name', 'FACHSE'),
        ];
    }
}
