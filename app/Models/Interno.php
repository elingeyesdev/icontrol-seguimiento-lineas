<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Interno
 *
 * @property $id
 * @property $estado
 * @property $micro_id
 * @property $conductor_id
 * @property $fecha_ingreso
 * @property $created_at
 * @property $updated_at
 *
 * @property Conductor[] $conductores
 * @property Micro $micro
 * @property Turno[] $turnos
 * @property Asignacion[] $asignaciones
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Interno extends Model
{
    
    protected $perPage = 20;

    protected $table = 'interno';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['estado', 'micro_id', 'fecha_ingreso'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function conductores()
    {
        return $this->belongsToMany(\App\Models\Conductor::class, 'conductor_interno', 'interno_id', 'conductor_id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function micro()
    {
        return $this->belongsTo(\App\Models\Micro::class, 'micro_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function turnos()
    {
        return $this->belongsToMany(\App\Models\Turno::class, 'interno_turno', 'interno_id', 'turno_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function asignaciones()
    {
        return $this->hasMany(\App\Models\Asignacion::class, 'interno_id');
    }
    
}
