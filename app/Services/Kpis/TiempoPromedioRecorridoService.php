<?php

namespace App\Services\Kpis;

use App\Models\AsignacionTurno;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TiempoPromedioRecorridoService
{
    public function obtenerIndicadores(array $filtros, User $usuario): array
    {
        $recorridos = $this->consulta($filtros, $usuario)->get()
            ->map(fn (AsignacionTurno $asignacion) => $this->calcularDuracion($asignacion))
            ->filter();

        return [
            'promedio' => $recorridos->isNotEmpty() ? round($recorridos->avg('duracion'), 1) : null,
            'recorridos' => $recorridos->count(),
            'evolucion' => $this->agrupar($recorridos, fn (array $fila) => $fila['asignacion']->fecha),
            'por_ruta' => $this->agrupar($recorridos, fn (array $fila) => $fila['asignacion']->ruta?->nombre ?? 'Sin ruta'),
            'por_conductor' => $this->agrupar($recorridos, fn (array $fila) => $fila['asignacion']->conductor?->nombre_completo ?? 'Sin conductor'),
            'por_micro' => $this->agrupar($recorridos, fn (array $fila) => $fila['asignacion']->micro?->placa ?? 'Sin micro'),
            'por_turno' => $this->agrupar($recorridos, fn (array $fila) => ucfirst($fila['asignacion']->turno?->nombre ?? 'Sin turno')),
        ];
    }

    private function consulta(array $filtros, User $usuario): Builder
    {
        return AsignacionTurno::query()
            ->where('estado', 'completado')
            ->whereNotNull('hora_salida')
            ->whereNotNull('hora_llegada')
            ->with(['ruta:id,nombre', 'conductor.user', 'conductor:id,nombre,apellido', 'micro:id,placa', 'turno:id,nombre'])
            ->when($filtros['fecha_inicio'] ?? null, fn (Builder $q, $fecha) => $q->whereDate('fecha', '>=', $fecha))
            ->when($filtros['fecha_fin'] ?? null, fn (Builder $q, $fecha) => $q->whereDate('fecha', '<=', $fecha))
            ->when($filtros['ruta_id'] ?? null, fn (Builder $q, $id) => $q->where('ruta_id', $id))
            ->when($filtros['conductor_id'] ?? null, fn (Builder $q, $id) => $q->where('conductor_id', $id))
            ->when($filtros['micro_id'] ?? null, fn (Builder $q, $id) => $q->where('micro_id', $id))
            ->when($filtros['turno_id'] ?? null, fn (Builder $q, $id) => $q->where('turno_id', $id))
            ->when($filtros['sentido'] ?? null, fn (Builder $q, $sentido) => $q->whereHas('ruta.rutaParadas', fn (Builder $paradas) => $paradas->where('sentido', $sentido)->where('estado', 'activo')))
            ->when($usuario->hasRole('propietario'), fn (Builder $q) => $q->whereHas('micro', fn (Builder $micro) => $micro->where('propietario_id', $usuario->propietario?->id ?? 0)));
    }

    private function calcularDuracion(AsignacionTurno $asignacion): ?array
    {
        $inicio = Carbon::parse($asignacion->fecha.' '.$asignacion->hora_salida);
        $fin = Carbon::parse($asignacion->fecha.' '.$asignacion->hora_llegada);
        $duracion = $inicio->diffInMinutes($fin, false);

        return $duracion > 0 ? ['asignacion' => $asignacion, 'duracion' => (float) $duracion] : null;
    }

    private function agrupar(Collection $recorridos, callable $clave): array
    {
        return $recorridos->groupBy($clave)->map(fn (Collection $grupo, string $nombre) => [
            'nombre' => $nombre,
            'promedio' => round($grupo->avg('duracion'), 1),
            'recorridos' => $grupo->count(),
        ])->values()->all();
    }
}
