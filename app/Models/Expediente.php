<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Persona;
use App\Models\Sucursal;
use App\Models\DetExpedienteDocumento;
use App\Models\DetExpedienteFase;
use App\Models\DetExpedienteCoautor;
use App\Models\FaiResultado;

class Expediente extends Model
{
    use HasFactory, SoftDeletes;

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
        'estado',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'fase_actual' => 'integer',
    ];

    // ==========================================
    // RELACIONES
    // ==========================================

    public function estudiante()
    {
        return $this->belongsTo(Persona::class, 'estudiante_id')
                    ->select(['id', 'nombre', 'apellido', 'email']);
    }

    public function asesor()
    {
        return $this->belongsTo(Persona::class, 'asesor_id')
                    ->select(['id', 'nombre', 'apellido', 'email']);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class)
                    ->select(['id', 'nombre']);
    }

    public function documentos()
    {
        return $this->hasMany(DetExpedienteDocumento::class, 'expediente_id');
    }

    public function fasesHistorial()
    {
        return $this->hasMany(DetExpedienteFase::class, 'expediente_id');
    }

    public function coautores()
    {
        return $this->hasMany(DetExpedienteCoautor::class, 'expediente_id');
    }

    public function resultadosFai()
    {
        return $this->hasMany(FaiResultado::class, 'expediente_id');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeSucursal(Builder $query, $sucursalId)
    {
        return $query->where('sucursal_id', $sucursalId);
    }

    public function scopeActivos(Builder $query)
    {
        return $query->whereNotIn('estado', ['cerrado', 'anulado']);
    }

    public function scopePorFase(Builder $query, $fase)
    {
        return $query->where('fase_actual', $fase);
    }
}
