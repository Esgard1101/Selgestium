<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Observacion extends Model
{
    use SoftDeletes;

    protected $table = 'det_expedienteobservacion';

    protected $fillable = [
        'expediente_id',
        'jurado_id',
        'ronda',
        'descripcion',
        'bloqueado',
        'tipo_veredicto'
    ];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class);
    }

    public function jurado()
    {
        return $this->belongsTo(Persona::class, 'jurado_id');
    }
}
