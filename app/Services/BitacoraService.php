<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BitacoraService
{
    /**
     * Guarda un registro en la bitácora de auditoría.
     */
    public function save(string $accion, ?int $id, string $data, int $table_id): void
    {
        DB::table('bitacora')->insert([
            'accion' => $accion,
            'usuario_id' => Auth::id(),
            'registro_id' => $id,
            'datos' => $data,
            'tabla_id' => $table_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
