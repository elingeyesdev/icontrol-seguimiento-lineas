<?php

namespace App\Services\Kpis;

use App\Models\AsignacionTurno;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CumplimientoParadasService
{
    public function obtenerIndicadores(array $filtros, User $usuario): array
    {
        $asignaciones = $this->consulta($filtros, $usuario)->get();
        $filas = $asignaciones->flatMap(fn (AsignacionTurno $asignacion) => $this->filas($asignacion, $filtros['sentido'] ?? null));
        $programadas = $filas->sum('programadas');
        $cumplidas = $filas->sum('cumplidas');

        return [
            'porcentaje' => $programadas > 0 ? round(($cumplidas / $programadas) * 100, 1) : null,
            'cumplidas' => $cumplidas,
            'programadas' => $programadas,
            'evolucion' => $this->agrupar($filas, 'fecha'),
            'por_ruta' => $this->agrupar($filas, 'ruta'),
            'por_conductor' => $this->agrupar($filas, 'conductor'),
            'por_micro' => $this->agrupar($filas, 'micro'),
            'por_turno' => $this->agrupar($filas, 'turno'),
        ];
    }

    private function consulta(array $filtros, User $usuario): Builder
    {
        return AsignacionTurno::query()
            ->whereIn('estado', ['en_curso', 'completado', 'retrasado'])
            ->with([
                'ruta:id,nombre',
                'ruta.rutaParadas:id,ruta_id,parada_id,orden,sentido,estado',
                'controlesRecorrido:id,asignacion_turno_id,ruta_parada_id,estado',
                'conductor.user',
                'conductor:id,nombre,apellido',
                'micro:id,placa',
                'turno:id,nombre',
            ])
            ->when($filtros['fecha_inicio'] ?? null, fn (Builder $q, $fecha) => $q->whereDate('fecha', '>=', $fecha))
            ->when($filtros['fecha_fin'] ?? null, fn (Builder $q, $fecha) => $q->whereDate('fecha', '<=', $fecha))
            ->when($filtros['ruta_id'] ?? null, fn (Builder $q, $id) => $q->where('ruta_id', $id))
            ->when($filtros['conductor_id'] ?? null, fn (Builder $q, $id) => $q->where('conductor_id', $id))
            ->when($filtros['micro_id'] ?? null, fn (Builder $q, $id) => $q->where('micro_id', $id))
            ->when($filtros['turno_id'] ?? null, fn (Builder $q, $id) => $q->where('turno_id', $id))
            ->when($filtros['sentido'] ?? null, fn (Builder $q, $sentido) => $q->whereHas('ruta.rutaParadas', fn (Builder $paradas) => $paradas->where('sentido', $sentido)->where('estado', 'activo')))
            ->when($usuario->hasRole('propietario'), fn (Builder $q) => $q->whereHas('micro', fn (Builder $micro) => $micro->where('propietario_id', $usuario->propietario?->id ?? 0)));
    }

    private function filas(AsignacionTurno $asignacion, ?string $sentido): Collection
    {
        $controles = $asignacion->controlesRecorrido
            ->whereNotNull('ruta_parada_id')
            ->groupBy('ruta_parada_id')
            ->map(fn (Collection $grupo) => $grupo->sortBy(fn ($control) => $control->estado === 'cumplido' ? 0 : 1)->first());

        return $asignacion->ruta?->rutaParadas
            ->where('estado', 'activo')
            ->when($sentido, fn (Collection $paradas) => $paradas->where('sentido', $sentido))
            ->map(fn ($parada) => [
                'fecha' => $asignacion->fecha,
                'ruta' => $asignacion->ruta->nombre,
                'conductor' => $asignacion->conductor?->nombre_completo ?? 'Sin conductor',
                'micro' => $asignacion->micro?->placa ?? 'Sin micro',
                'turno' => ucfirst($asignacion->turno?->nombre ?? 'Sin turno'),
                'programadas' => 1,
                'cumplidas' => $controles->get($parada->id)?->estado === 'cumplido' ? 1 : 0,
            ])->values() ?? collect();
    }

    private function agrupar(Collection $filas, string $campo): array
    {
        return $filas->groupBy($campo)->map(fn (Collection $grupo, string $nombre) => [
            'nombre' => $nombre,
            'cumplidas' => $grupo->sum('cumplidas'),
            'programadas' => $grupo->sum('programadas'),
            'porcentaje' => $grupo->sum('programadas') > 0 ? round(($grupo->sum('cumplidas') / $grupo->sum('programadas')) * 100, 1) : null,
        ])->values()->all();
    }
}
