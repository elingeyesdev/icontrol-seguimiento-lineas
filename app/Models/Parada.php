<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Parada
 *
 * @property $id
 * @property $nombre
 * @property $ubicacion
 * @property $created_at
 * @property $updated_at
 *
 * @property Ruta[] $rutas
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Parada extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nombre', 'ubicacion'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function rutas()
    {
        return $this->belongsToMany(\App\Models\Ruta::class, 'parada_ruta', 'parada_id', 'ruta_id');
    }
    
}
