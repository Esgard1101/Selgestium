<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Persona extends Model
{
    use SoftDeletes;

    protected $table = 'persona';

    protected $fillable = ['nombre', 'apellido', 'dni', 'email', 'sucursal_id', 'creditos'];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function usuario(): HasOne
    {
        return $this->hasOne(User::class, 'persona_id');
    }
}
