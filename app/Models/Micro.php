<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Micro
 *
 * @property $id
 * @property $dueño_id
 * @property $placa
 * @property $modelo
 * @property $marca
 * @property $capacidad_pasajeros
 * @property $created_at
 * @property $updated_at
 *
 * @property Dueño $dueño
 * @property Interno[] $internos
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Micro extends Model
{
    
    protected $perPage = 20;

    protected $table = 'micro';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['dueño_id', 'placa', 'modelo', 'marca', 'capacidad_pasajeros'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dueño()
    {
        return $this->belongsTo(\App\Models\Dueño::class, 'dueño_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function internos()
    {
        return $this->hasMany(\App\Models\Interno::class, 'micro_id', 'id');
    }
    
}
