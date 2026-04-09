<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Traits\DataBaseTrait;

class FaiDsaService
{
    use DataBaseTrait;

    public const FAI_CODIGO = 'RF02.1';

    /**
     * Verificación manual de créditos académicos.
     *
     * La UI (rol: ui) consulta el expediente, ingresa los créditos
     * que el estudiante acredita y el sistema evalúa contra el umbral
     * CREDITOS_MINIMOS del parámetro global (con override por sucursal).
     *
     * @param int    $expedienteId     ID del expediente en fase 2
     * @param int    $creditosIngresados Créditos digitados por el operador
     * @param int    $validadoPorId    persona_id del usuario UI que valida
     * @param string $ip               IP del actor (request()->ip())
     * @return object Registro insertado en fairesultado
     */
    public function verificarManual(
        int    $expedienteId,
        int    $creditosIngresados,
        int    $validadoPorId,
        string $ip
    ): object {
        $sucursalId = DB::table('expediente')
            ->where('id', $expedienteId)
            ->value('sucursal_id');

        $umbral = $this->resolverUmbral($sucursalId);
        $estado = $creditosIngresados >= $umbral ? 'aprobado' : 'rechazado';

        // fairesultado es inmutable — solo created_at, sin updated_at
        $id = DB::table('fairesultado')->insertGetId([
            'expediente_id'   => $expedienteId,
            'fai_codigo'      => self::FAI_CODIGO,
            'apifuente_id'    => DB::table('apifuente')->where('codigo', 'DSA')->value('id'),
            'estado'          => $estado,
            'valor_obtenido'  => $creditosIngresados . ' créditos',
            'valor_umbral'    => $umbral . ' créditos mín.',
            'respuesta_raw'   => json_encode([
                'modo'              => 'manual',
                'creditos_digitados' => $creditosIngresados,
                'umbral_aplicado'   => $umbral,
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
     * Verificación automática vía DSA API.
     *
     * TODO: Sprint 2 — integrar DSA API de UNPRG.
     * Cuando modo_operacion = 'api' en faiconfig (RF02.1), este método
     * tomará las credenciales de faiconfig.url_servicio / token_servicio,
     * consultará el portal del DSA por código de alumno y registrará
     * el resultado sin intervención manual.
     */
    public function verificarApi(int $expedienteId): void
    {
        // TODO: Sprint 2 — integrar DSA API
    }

    /**
     * Retorna el umbral vigente de créditos para la sucursal dada.
     * Primero busca override por sucursal; si no existe, usa el global.
     */
    private function resolverUmbral(int $sucursalId): int
    {
        $valor = DB::table('parametro')
            ->where('codigo', 'CREDITOS_MINIMOS')
            ->where('sucursal_id', $sucursalId)
            ->whereNull('deleted_at')
            ->value('valor');

        if ($valor === null) {
            $valor = DB::table('parametro')
                ->where('codigo', 'CREDITOS_MINIMOS')
                ->whereNull('sucursal_id')
                ->whereNull('deleted_at')
                ->value('valor');
        }

        return (int) ($valor ?? 160);
    }

    /**
     * Retorna el umbral vigente para mostrarlo en la vista, dado un expediente.
     */
    public function umbralVigente(int $expedienteId): int
    {
        $sucursalId = DB::table('expediente')
            ->where('id', $expedienteId)
            ->value('sucursal_id');

        return $this->resolverUmbral((int) $sucursalId);
    }

    /**
     * Retorna el último resultado FAI RF02.1 para un expediente, o null.
     */
    public function ultimoResultado(int $expedienteId): ?object
    {
        return DB::table('fairesultado')
            ->where('expediente_id', $expedienteId)
            ->where('fai_codigo', self::FAI_CODIGO)
            ->orderByDesc('created_at')
            ->first();
    }
}
