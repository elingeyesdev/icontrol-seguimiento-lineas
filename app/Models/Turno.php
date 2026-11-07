<?php

namespace App\Models;

use App\Models\Concerns\TieneEstadoLogico;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo Turno – catálogo estático de horarios de operación.
 *
 * Un turno representa un bloque horario (Mañana / Tarde / Noche).
 * NO se crea un registro por cada día; es un parámetro reutilizable.
 *
 * @property int $id
 * @property string $nombre Enum: mañana | tarde | noche
 * @property string $hora_inicio Hora habitual de inicio (HH:MM:SS)
 * @property string $hora_fin Hora habitual de finalización (HH:MM:SS)
 * @property string|null $descripcion
 * @property string $estado activo | inactivo
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Turno extends Model
{
    use TieneEstadoLogico;

    protected $table = 'turno';

    protected $perPage = 20;

    protected $fillable = [
        'nombre',
        'hora_inicio',
        'hora_fin',
        'descripcion',
        'estado',
    ];

    /**
     * Etiquetas legibles para el enum `nombre`.
     */
    public const NOMBRES = [
        'mañana' => 'Mañana',
        'tarde' => 'Tarde',
        'noche' => 'Noche',
    ];

    /**
     * Devuelve la etiqueta capitalizada del turno.
     */
    public function getNombreLabelAttribute(): string
    {
        return self::NOMBRES[$this->nombre] ?? ucfirst($this->nombre);
    }

    /**
     * Accessor para el ícono y color del turno (mañana, tarde, noche).
     */
    protected function turnoBadge(): Attribute
    {
        return Attribute::get(function () {
            $config = [
                'mañana' => ['icono' => 'bi-sun', 'bg' => '#fff8e1', 'color' => '#f9a825'],
                'tarde' => ['icono' => 'bi-cloud-sun', 'bg' => '#fff3e0', 'color' => '#ef6c00'],
                'noche' => ['icono' => 'bi-moon-stars', 'bg' => '#ede7f6', 'color' => '#5e35b1'],
            ];

            return $config[$this->nombre] ?? ['icono' => 'bi-clock', 'bg' => '#e3f2fd', 'color' => '#0B3C78'];
        });
    }

    /**
     * Accessor para el badge de estado activo/inactivo.
     */
    protected function estadoBadge(): Attribute
    {
        return Attribute::get(function () {
            if ($this->estado === 'activo') {
                return ['bg' => '#e6f4ea', 'color' => '#1e7e34', 'label' => 'Activo', 'icono' => 'bi-check-circle-fill'];
            }

            return ['bg' => '#f0f0f0', 'color' => '#6c757d', 'label' => 'Inactivo', 'icono' => 'bi-dash-circle-fill'];
        });
    }

    /**
     * Un turno puede estar asociado a muchas asignaciones de turno.
     *
     * @return HasMany
     */
    public function asignaciones()
    {
        return $this->hasMany(AsignacionTurno::class, 'turno_id');
    }
}
