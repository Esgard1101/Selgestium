<?php

namespace App\Services;

use App\Traits\DataBaseTrait;
use Illuminate\Support\Facades\DB;
use Exception;

class JuradoService
{
    use DataBaseTrait;

    /**
     * Asignar 3 jurados a un expediente.
     *
     * @param int $expedienteId
     * @param array $juradoIds Array de IDs de personas que serán jurados.
     * @return void
     * @throws Exception
     */
    public function asignarJurados(int $expedienteId, array $juradoIds): void
    {
        if (count($juradoIds) !== 3) {
            throw new Exception("Se deben asignar exactamente 3 jurados.");
        }

        DB::transaction(function () use ($expedienteId, $juradoIds) {
            // Verificar que el expediente existe
            $expediente = DB::table('expediente')->where('id', $expedienteId)->first();
            if (!$expediente) {
                throw new Exception("El expediente no existe.");
            }

            foreach ($juradoIds as $juradoId) {
                // Verificar que el jurado (persona) existe
                $persona = DB::table('persona')->where('id', $juradoId)->first();
                if (!$persona) {
                    throw new Exception("El jurado con ID {$juradoId} no existe.");
                }

                $this->insertSingleDB('det_expedientejurado', 0, [
                    'expediente_id' => $expedienteId,
                    'jurado_id' => $juradoId,
                ]);
            }
        });
    }
}
