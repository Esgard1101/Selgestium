<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Persona extends Model
{
    use SoftDeletes;

    protected $table = 'persona';

    protected $fillable = ['nombre', 'apellido', 'dni', 'email', 'sucursal_id', 'creditos'];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}
