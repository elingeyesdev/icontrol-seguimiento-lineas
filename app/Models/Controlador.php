<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Controlador
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
 * @property Horario[] $horarios
 * @package App\Models
 */
class Controlador extends Model
{
    use HasFactory;

    protected $table = 'controlador';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'apellido',
        'telefono',
        'correo',
        'ci',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function horarios()
    {
        return $this->hasMany(Horario::class, 'controlador_id');
    }
}
