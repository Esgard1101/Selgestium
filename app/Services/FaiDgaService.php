<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Traits\DataBaseTrait;

class FaiDgaService
{
    use DataBaseTrait;

    public const FAI_CODIGO = 'RF02.4';

    /**
     * Verificación manual de voucher de sustentación.
     *
     * Aplica solo en Etapa II (fase 8+). Retorna 'no_aplica' si se ejecuta en Etapa I.
     */
    public function verificarSustentacionManual(
        int    $expedienteId,
        int    $validadoPorId,
        string $ip
    ) {
        $expediente = DB::table('expediente')->where('id', $expedienteId)->first();

        if (!$expediente) {
            return 'no_aplica';
        }

        if ($expediente->etapa === 'I') {
            return 'no_aplica';
        }

        $estado = 'aprobado';

        $id = DB::table('fairesultado')->insertGetId([
            'expediente_id'   => $expedienteId,
            'fai_codigo'      => self::FAI_CODIGO,
            'apifuente_id'    => DB::table('apifuente')->where('codigo', 'DGA')->value('id'),
            'estado'          => $estado,
            'valor_obtenido'  => 'Voucher validado',
            'valor_umbral'    => 'Requerido',
            'respuesta_raw'   => json_encode([
                'modo' => 'manual',
            ]),
            'motivorechazo_id' => null,
            'validado_por_id'  => $validadoPorId,
            'ip_actor'         => $ip,
            'verificado_at'    => now(),
            'created_at'       => now(),
        ]);

        return DB::table('fairesultado')->find($id);
    }

    /**
     * Verificación automática vía DGA API.
     *
     * TODO: Sprint 2 — integrar DGA API
     */
    public function verificarSustentacionApi(int $expedienteId): void
    {
        // TODO: Sprint 2 — integrar DGA API
    }

    public function ultimoResultado(int $expedienteId): ?object
    {
        return DB::table('fairesultado')
            ->where('expediente_id', $expedienteId)
            ->where('fai_codigo', self::FAI_CODIGO)
            ->orderByDesc('created_at')
            ->first();
    }
}
