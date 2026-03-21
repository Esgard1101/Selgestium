<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use App\Models\DetExpedienteDocumento;
use App\Models\DetExpedienteFase;
use App\Models\DetExpedienteCoautor;
use App\Models\FaiResultado;


class Expediente extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Apuntar a la tabla exacta (en singular, como dicta tu migración)
    protected $table = 'expediente';

    // 2. Atributos asignables (Aunque usaremos DataBaseTrait para insertar, 
    // es buena práctica tenerlos para las consultas)
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

    // 3. Casteos de tipos de datos
    protected $casts = [
        'fase_actual' => 'integer',
    ];

    // ==========================================
    // RELACIONES (Basadas en tu arquitectura)
    // ==========================================

    public function estudiante()
    {
        return $this->belongsTo(User::class, 'estudiante_id');
    }

    public function asesor()
    {
        return $this->belongsTo(User::class, 'asesor_id');
    }

    // Archivos PDF adjuntos
    public function documentos()
    {
        return $this->hasMany(DetExpedienteDocumento::class, 'expediente_id');
    }

    // Historial de fases y trazabilidad
    public function fasesHistorial()
    {
        return $this->hasMany(DetExpedienteFase::class, 'expediente_id');
    }

    // Coautores (máx. 2 en pregrado)
    public function coautores()
    {
        return $this->hasMany(DetExpedienteCoautor::class, 'expediente_id');
    }

    // Resultados de validaciones automáticas (Módulo FAI)
    public function resultadosFai()
    {
        return $this->hasMany(FaiResultado::class, 'expediente_id');
    }


    // Scope: sucursal 
    public function scopeSucursal(Builder $query, $sucursalId)
    {
        return $query->where('sucursal_id', $sucursalId);
    }

    // Scope: activos (Que no estén cerrados ni anulados)
    public function scopeActivos(Builder $query)
    {
        return $query->whereNotIn('estado', ['cerrado', 'anulado']);
    }

    // Scope: porFase
    public function scopePorFase(Builder $query, $fase)
    {
        return $query->where('fase_actual', $fase);
    }
}
