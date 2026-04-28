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
 * @property Turno[] $turnos
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
    protected $fillable = ['origen', 'destino', 'nombre_ruta'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function turnos()
    {
        return $this->hasMany(\App\Models\Turno::class, 'ruta_id', 'id');
    }
    
}
