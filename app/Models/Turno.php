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
 * @property Interno $interno
 * @property Ruta $ruta
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
    protected $fillable = ['interno_id', 'ruta_id', 'hora_inicio', 'hora_fin', 'fecha_laboral'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function interno()
    {
        return $this->belongsTo(\App\Models\Interno::class, 'interno_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ruta()
    {
        return $this->belongsTo(\App\Models\Ruta::class, 'ruta_id', 'id');
    }
    
}
