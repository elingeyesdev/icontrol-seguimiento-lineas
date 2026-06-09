<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Ruta
 *
 * @property $id
 * @property $origen
 * @property $destino
 * @property $nombre_ruta
 * @property $created_at
 * @property $updated_at
 *
 * @property Turno $turno
 * @property Parada[] $paradas
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Ruta extends Model
{
    
    protected $perPage = 20;

    protected $table = 'ruta';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['origen', 'destino', 'nombre_ruta', 'turno_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function turno()
    {
        return $this->belongsTo(\App\Models\Turno::class, 'turno_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function paradas()
    {
        return $this->belongsToMany(\App\Models\Parada::class, 'parada_ruta', 'ruta_id', 'parada_id');
    }
    
}
