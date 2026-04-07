<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RolPersona extends Model
{
    use SoftDeletes;

    protected $table = 'rolpersona';

    protected $fillable = ['persona_id', 'usuario_id', 'rol_id', 'sucursal_id'];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }
}
