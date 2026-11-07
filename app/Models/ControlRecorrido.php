<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo ControlRecorrido - Evaluación automática de recorrido asociado a una asignación de turno.
 *
 * @property int $id
 * @property int $asignacion_turno_id
 * @property int|null $ruta_parada_id
 * @property string|null $fecha_hora
 * @property string $estado pendiente | cumplido | omitido | fuera_ruta | en_curso | completado | cancelado
 * @property float|null $distancia_metros
 * @property string|null $observacion
 */
class ControlRecorrido extends Model
{
    use HasFactory;

    protected $table = 'control_recorrido';

    protected $fillable = [
        'asignacion_turno_id',
        'ruta_parada_id',
        'fecha_hora',
        'estado',
        'distancia_metros',
        'observacion',
    ];

    protected function casts(): array
    {
        return [
            'fecha_hora' => 'datetime',
            'distancia_metros' => 'float',
        ];
    }

    /**
     * Asignación de turno a la que pertenece este registro de control.
     */
    public function asignacionTurno(): BelongsTo
    {
        return $this->belongsTo(AsignacionTurno::class, 'asignacion_turno_id');
    }

    /**
     * Parada de la ruta evaluada en este control.
     */
    public function rutaParada(): BelongsTo
    {
        return $this->belongsTo(RutaParada::class, 'ruta_parada_id');
    }

    /**
     * Alias de rutaParada para compatibilidad.
     */
    public function paradaRuta(): BelongsTo
    {
        return $this->rutaParada();
    }

    /**
     * Posiciones GPS registradas durante este recorrido.
     */
    public function seguimientosGps(): HasMany
    {
        return $this->hasMany(SeguimientoGps::class, 'control_recorrido_id');
    }
}
