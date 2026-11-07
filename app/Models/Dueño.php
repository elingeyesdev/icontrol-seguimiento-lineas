<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Dueño
 *
 * @property $id
 * @property $nombre
 * @property $apellido
 * @property $telefono
 * @property $correo
 * @property $ci
 * @property $created_at
 * @property $updated_at
 *
 * @property Micro[] $micros
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Dueño extends Model
{
    
    protected $perPage = 20;

    protected $table = 'dueño';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nombre', 'apellido', 'telefono', 'correo', 'ci'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function micros()
    {
        return $this->hasMany(\App\Models\Micro::class, 'dueño_id', 'id');
    }
    
}
