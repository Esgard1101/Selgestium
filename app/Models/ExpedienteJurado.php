<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpedienteJurado extends Model
{
    use SoftDeletes;

    protected $table = 'det_expedientejurado';

    protected $fillable = ['expediente_id', 'jurado_id'];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class);
    }

    public function jurado()
    {
        return $this->belongsTo(Persona::class, 'jurado_id');
    }
}
