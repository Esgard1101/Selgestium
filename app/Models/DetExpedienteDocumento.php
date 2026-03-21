<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetExpedienteDocumento extends Model
{
    // Dile a Laravel el nombre exacto en singular (revisa cómo se llama en tu pgAdmin)
    protected $table = 'det_expedientedocumento';

    // Opcional por ahora, pero buena práctica:
    protected $guarded = []; // Permite guardar cualquier campo
}
