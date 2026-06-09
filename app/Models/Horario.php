<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Horario
 *
 * @property $id
 * @property $hora_inicio
 * @property $hora_fin
 * @property $descripcion
 * @property $controlador_id
 * @property $created_at
 * @property $updated_at
 *
 * @property Controlador $controlador
 * @property Asignacion[] $asignaciones
 * @package App\Models
 */
class Horario extends Model
{
    use HasFactory;

    protected $table = 'horario';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'hora_inicio',
        'hora_fin',
        'descripcion',
        'controlador_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function controlador()
    {
        return $this->belongsTo(Controlador::class, 'controlador_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function asignaciones()
    {
        return $this->hasMany(Asignacion::class, 'horario_id');
    }
}
