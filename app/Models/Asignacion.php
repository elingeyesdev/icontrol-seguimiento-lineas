<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Asignacion
 *
 * @property $id
 * @property $turno_id
 * @property $interno_id
 * @property $horario_id
 * @property $created_at
 * @property $updated_at
 *
 * @property Turno $turno
 * @property Interno $interno
 * @property Horario $horario
 * @package App\Models
 */
class Asignacion extends Model
{
    use HasFactory;

    protected $table = 'asigna';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'turno_id',
        'interno_id',
        'horario_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function turno()
    {
        return $this->belongsTo(Turno::class, 'turno_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function interno()
    {
        return $this->belongsTo(Interno::class, 'interno_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function horario()
    {
        return $this->belongsTo(Horario::class, 'horario_id');
    }
}
