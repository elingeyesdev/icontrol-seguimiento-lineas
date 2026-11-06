<?php

namespace App\Models;

use App\Models\Concerns\TieneEstadoLogico;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class Ruta
 *
 * @property int $id
 * @property string $nombre
 * @property string $descripcion
 * @property string $estado
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Collection<Parada> $paradas
 * @property Collection<Parada> $paradasIda
 * @property Collection<Parada> $paradasVuelta
 * @property Collection<Turno> $turnos
 *
 * @mixin Builder
 */
class Ruta extends Model
{
    use HasFactory, TieneEstadoLogico;

    protected $perPage = 20;

    protected $table = 'ruta';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nombre', 'descripcion', 'estado'];

    // ─── Relaciones ────────────────────────────────────────────────────────

    /**
     * Turnos asignados a esta ruta (a través de asignacion_turnos).
     * Nota de arquitectura: la tabla turno es un catálogo de horarios sin columna ruta_id.
     */
    public function turnosAsignados()
    {
        return $this->hasManyThrough(
            Turno::class,
            AsignacionTurno::class,
            'ruta_id',  // FK en asignacion_turnos
            'id',       // PK en turno
            'id',       // PK en ruta
            'turno_id'  // FK en asignacion_turnos hacia turno
        )->distinct();
    }

    /**
     * Alias compatible de turnos asignados a esta ruta.
     */
    public function turnos()
    {
        return $this->turnosAsignados();
    }

    /**
     * Asignaciones de turno de esta ruta.
     */
    public function asignacionesTurno()
    {
        return $this->hasMany(AsignacionTurno::class, 'ruta_id');
    }

    /**
     * Relación directa con la tabla pivote con metadatos de orden y sentido.
     */
    public function rutaParadas()
    {
        return $this->hasMany(RutaParada::class, 'ruta_id');
    }

    /**
     * Todas las paradas de la ruta (Ida + Vuelta), ordenadas por orden dentro de cada sentido.
     * Útil para obtener el total de paradas o para carga eager genérica.
     */
    public function paradas()
    {
        return $this->belongsToMany(Parada::class, 'parada_ruta', 'ruta_id', 'parada_id')
            ->withPivot(['orden', 'sentido', 'estado'])
            ->orderByPivot('sentido')
            ->orderByPivot('orden')
            ->withTimestamps();
    }

    /**
     * Paradas del sentido Ida, ordenadas por orden ascendente.
     */
    public function paradasIda()
    {
        return $this->belongsToMany(Parada::class, 'parada_ruta', 'ruta_id', 'parada_id')
            ->withPivot(['orden', 'sentido', 'estado'])
            ->wherePivot('sentido', 'Ida')
            ->orderByPivot('orden')
            ->withTimestamps();
    }

    /**
     * Paradas del sentido Vuelta, ordenadas por orden ascendente.
     */
    public function paradasVuelta()
    {
        return $this->belongsToMany(Parada::class, 'parada_ruta', 'ruta_id', 'parada_id')
            ->withPivot(['orden', 'sentido', 'estado'])
            ->wherePivot('sentido', 'Vuelta')
            ->orderByPivot('orden')
            ->withTimestamps();
    }
}
