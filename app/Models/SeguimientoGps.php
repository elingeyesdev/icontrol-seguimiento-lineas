<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * Modelo SeguimientoGps - Registro de posiciones geográficas enviadas por el dispositivo móvil.
 *
 * Pertenece exclusivamente a ControlRecorrido (sin FK directa a asignacion_turnos).
 *
 * @property int $id
 * @property int $control_recorrido_id
 * @property string $fecha_hora_gps
 * @property float $latitud
 * @property float $longitud
 * @property float|null $velocidad
 * @property string|null $fecha_hora_sincronizacion
 */
class SeguimientoGps extends Model
{
    use HasFactory;

    protected $table = 'seguimiento_gps';

    protected $fillable = [
        'control_recorrido_id',
        'fecha_hora_gps',
        'latitud',
        'longitud',
        'velocidad',
        'fecha_hora_sincronizacion',
    ];

    protected function casts(): array
    {
        return [
            'fecha_hora_gps' => 'datetime',
            'fecha_hora_sincronizacion' => 'datetime',
            'latitud' => 'float',
            'longitud' => 'float',
            'velocidad' => 'float',
        ];
    }

    /**
     * Recorrido (sesión) al que pertenece esta posición GPS.
     */
    public function controlRecorrido(): BelongsTo
    {
        return $this->belongsTo(ControlRecorrido::class, 'control_recorrido_id');
    }

    /**
     * Asignación de turno obtenida a través de ControlRecorrido.
     */
    public function asignacionTurno(): HasOneThrough
    {
        return $this->hasOneThrough(
            AsignacionTurno::class,
            ControlRecorrido::class,
            'id',                   // Local key on ControlRecorrido (matches control_recorrido_id)
            'id',                   // Local key on AsignacionTurno (matches asignacion_turno_id)
            'control_recorrido_id', // Foreign key on SeguimientoGps
            'asignacion_turno_id'   // Foreign key on ControlRecorrido
        );
    }
}
