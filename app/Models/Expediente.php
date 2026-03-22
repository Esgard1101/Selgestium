<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expediente extends Model
{
    use SoftDeletes;

    protected $table = 'expediente';

    protected $fillable = [
        'numero_radicacion',
        'estudiante_id',
        'asesor_id',
        'sucursal_id',
        'titulo',
        'tipo',
        'etapa',
        'fase_actual',
        'estado'
    ];

    public function estudiante()
    {
        return $this->belongsTo(Persona::class, 'estudiante_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}
