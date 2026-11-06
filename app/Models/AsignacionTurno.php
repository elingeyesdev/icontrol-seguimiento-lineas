<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Modelo AsignacionTurno – entidad transaccional diaria.
 *
 * Representa: en una fecha determinada, un conductor conduce un micro/interno
 * en un turno determinado sobre una ruta determinada.
 *
 * @property int $id
 * @property string $fecha
 * @property int $turno_id
 * @property int $ruta_id
 * @property int $micro_id
 * @property int $conductor_id
 * @property string|null $hora_salida
 * @property string|null $hora_llegada
 * @property string $estado pendiente | en_curso | completado | retrasado | cancelado
 * @property string|null $observaciones
 */
class AsignacionTurno extends Model
{
    protected $table = 'asignacion_turnos';

    protected $fillable = [
        'fecha',
        'turno_id',
        'ruta_id',
        'micro_id',
        'conductor_id',
        'hora_salida',
        'hora_llegada',
        'estado',
        'observaciones',
    ];

    // ─── Scopes ────────────────────────────────────────────────────────

    public function scopeBuscarPorConductor(Builder $query, string $criterio): Builder
    {
        return $query->where(function ($q) use ($criterio) {
            $q->whereHas('conductor', function ($c) use ($criterio) {
                $c->where('nombre', 'like', "%{$criterio}%")
                    ->orWhere('apellido', 'like', "%{$criterio}%");
            })->orWhere('fecha', 'like', "%{$criterio}%");
        });
    }

    public function scopeNoCanceladas(Builder $query): Builder
    {
        return $query->where('estado', '!=', 'cancelado');
    }

    // ─── Accessors ─────────────────────────────────────────────────────

    protected function estadoBadge(): Attribute
    {
        return Attribute::get(function () {
            $badges = [
                'pendiente' => ['bg' => '#fff9c4', 'color' => '#795548', 'label' => 'Pendiente'],
                'en_curso' => ['bg' => '#e3f2fd', 'color' => '#1565c0', 'label' => 'En curso'],
                'completado' => ['bg' => '#e6f4ea', 'color' => '#1e7e34', 'label' => 'Completado'],
                'retrasado' => ['bg' => '#fff3e0', 'color' => '#e65100', 'label' => 'Retrasado'],
                'cancelado' => ['bg' => '#f0f0f0', 'color' => '#6c757d', 'label' => 'Cancelado'],
            ];

            return $badges[$this->estado] ?? ['bg' => '#f0f0f0', 'color' => '#6c757d', 'label' => ucfirst($this->estado)];
        });
    }

    protected function turnoEmoji(): Attribute
    {
        return Attribute::get(function () {
            $iconos = ['mañana' => '☀️', 'tarde' => '🌤️', 'noche' => '🌙'];
            $nombre = $this->turno?->nombre ?? '';

            return $iconos[$nombre] ?? '';
        });
    }

    public function estaActivo(): bool
    {
        return ! in_array($this->estado, ['cancelado', 'completado']);
    }

    // ─── Relaciones ────────────────────────────────────────────────────

    /** @return BelongsTo */
    public function turno()
    {
        return $this->belongsTo(Turno::class, 'turno_id');
    }

    /** @return BelongsTo */
    public function ruta()
    {
        return $this->belongsTo(Ruta::class, 'ruta_id');
    }

    /** @return BelongsTo */
    public function micro()
    {
        return $this->belongsTo(Micro::class, 'micro_id');
    }

    /** @return BelongsTo */
    public function conductor()
    {
        return $this->belongsTo(Conductor::class, 'conductor_id');
    }

    /**
     * Controles de recorrido ejecutados para esta asignación.
     */
    public function controlesRecorrido(): HasMany
    {
        return $this->hasMany(ControlRecorrido::class, 'asignacion_turno_id');
    }

    /**
     * Alias de controlesRecorrido para pluralización.
     */
    public function controlesRecorridos(): HasMany
    {
        return $this->controlesRecorrido();
    }

    /**
     * Recorrido activo o más reciente de la asignación.
     */
    public function controlRecorrido(): HasOne
    {
        return $this->hasOne(ControlRecorrido::class, 'asignacion_turno_id')->latestOfMany();
    }

    /**
     * Posiciones GPS del micro obtenidas a través de la sesión de ControlRecorrido (Asignacion -> Control -> Seguimiento).
     */
    public function seguimientosGps(): HasManyThrough
    {
        return $this->hasManyThrough(
            SeguimientoGps::class,
            ControlRecorrido::class,
            'asignacion_turno_id',     // FK en control_recorrido hacia asignacion_turnos
            'control_recorrido_id',    // FK en seguimiento_gps hacia control_recorrido
            'id',                      // Local key en asignacion_turnos
            'id'                       // Local key en control_recorrido
        );
    }
}
