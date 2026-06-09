<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Turno
 *
 * @property $id
 * @property $interno_id
 * @property $ruta_id
 * @property $hora_inicio
 * @property $hora_fin
 * @property $fecha_laboral
 * @property $created_at
 * @property $updated_at
 *
 * @property Interno[] $internos
 * @property Ruta[] $rutas
 * @property Asignacion[] $asignaciones
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Turno extends Model
{
    
    protected $perPage = 20;

    protected $table = 'turno';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['hora_inicio', 'hora_fin', 'fecha_laboral'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function internos()
    {
        return $this->belongsToMany(\App\Models\Interno::class, 'interno_turno', 'turno_id', 'interno_id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rutas()
    {
        return $this->hasMany(\App\Models\Ruta::class, 'turno_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function asignaciones()
    {
        return $this->hasMany(\App\Models\Asignacion::class, 'turno_id');
    }
    
}
