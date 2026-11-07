<?php

namespace App\Reportes;

use App\Models\Conductor;
use App\Models\Ruta;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Reporte: Paradas Omitidas por Ruta y Conductor.
 *
 * Una parada se considera omitida cuando la unidad no registró su paso
 * (estado `cumplido`) durante un recorrido finalizado. El reporte combina:
 *
 *  1. Paradas omitidas registradas explícitamente (control_recorrido.estado = 'omitido').
 *  2. Paradas derivadas: recorridos `completado` cuyas paradas activas de la
 *     ruta no tienen un control `cumplido`.
 */
class ReporteParadasOmitidas extends ReporteBase
{
    public function titulo(): string
    {
        return 'Paradas Omitidas por Ruta y Conductor';
    }

    public function descripcion(): string
    {
        return 'Detalle de las paradas que no fueron cumplidas por las unidades, filtrable por ruta, conductor y rango de fechas.';
    }

    public function columnas(): array
    {
        return [
            'fecha' => 'Fecha',
            'hora' => 'Hora',
            'turno' => 'Turno',
            'conductor' => 'Conductor',
            'ruta' => 'Ruta',
            'micro' => 'Micro / Interno',
            'parada' => 'Parada omitida',
            'sentido' => 'Sentido',
            'orden' => 'Orden',
            'origen' => 'Origen',
        ];
    }

    public function resultados(array $filtros): Collection
    {
        $filas = collect();

        $this->consultaDerivadas($filtros)->get()->each(function (object $fila) use ($filas) {
            $fila->origen = 'Derivado';
            $filas->put($this->clave($fila), $fila);
        });

        $this->consultaRegistradas($filtros)->get()->each(function (object $fila) use ($filas) {
            $fila->origen = 'Registrado';
            $filas->put($this->clave($fila), $fila);
        });

        return $filas
            ->sortBy(fn (object $fila) => sprintf(
                '%s|%s|%s|%s|%05d',
                $fila->fecha,
                $fila->ruta_nombre,
                $fila->conductor_nombre,
                $fila->sentido,
                (int) $fila->orden
            ))
            ->map(fn (object $fila) => $this->formatear($fila))
            ->values();
    }

    public function resumen(Collection $filas): array
    {
        return [
            'Total de paradas omitidas' => $filas->count(),
            'Rutas afectadas' => $filas->pluck('ruta')->unique()->count(),
            'Conductores involucrados' => $filas->pluck('conductor')->unique()->count(),
            'Paradas distintas' => $filas->pluck('parada')->unique()->count(),
        ];
    }

    public function filtrosAplicados(array $filtros): array
    {
        $rutaId = $filtros['ruta_id'] ?? null;
        $conductorId = $filtros['conductor_id'] ?? null;
        $desde = $filtros['desde'] ?? null;
        $hasta = $filtros['hasta'] ?? null;

        $ruta = $rutaId ? Ruta::find($rutaId) : null;
        $conductor = $conductorId ? Conductor::with('user')->find($conductorId) : null;

        return array_filter([
            'Ruta' => $ruta?->nombre,
            'Conductor' => $conductor?->nombre_completo,
            'Desde' => $desde ? Carbon::parse($desde)->format('d/m/Y') : null,
            'Hasta' => $hasta ? Carbon::parse($hasta)->format('d/m/Y') : null,
        ], fn ($valor) => $valor !== null && $valor !== '');
    }

    public function opcionesFiltros(): array
    {
        return [
            'rutas' => Ruta::where('estado', 'activo')->orderBy('nombre')->get(),
            'conductores' => Conductor::with('user')->orderBy('nombre')->get(),
        ];
    }

    /**
     * Paradas sin control `cumplido` en recorridos finalizados.
     *
     * @param  array<string, mixed>  $filtros
     */
    private function consultaDerivadas(array $filtros)
    {
        return DB::table('asignacion_turnos as at')
            ->join('ruta as r', 'r.id', '=', 'at.ruta_id')
            ->join('parada_ruta as rp', function ($join) {
                $join->on('rp.ruta_id', '=', 'at.ruta_id')
                    ->where('rp.estado', '=', 'activo');
            })
            ->join('paradas as pa', 'pa.id', '=', 'rp.parada_id')
            ->leftJoin('conductor as c', 'c.id', '=', 'at.conductor_id')
            ->leftJoin('users as uc', 'uc.id', '=', 'c.user_id')
            ->leftJoin('micro as m', 'm.id', '=', 'at.micro_id')
            ->leftJoin('interno as i', 'i.id', '=', 'm.interno_id')
            ->leftJoin('turno as t', 't.id', '=', 'at.turno_id')
            ->select($this->seleccion(['at.fecha as fecha', DB::raw('NULL as fecha_hora')]))
            ->where('at.estado', 'completado')
            ->when($filtros['ruta_id'] ?? null, fn ($query, $rutaId) => $query->where('at.ruta_id', $rutaId))
            ->when($filtros['conductor_id'] ?? null, fn ($query, $conductorId) => $query->where('at.conductor_id', $conductorId))
            ->when($filtros['desde'] ?? null, fn ($query, $desde) => $query->whereDate('at.fecha', '>=', $desde))
            ->when($filtros['hasta'] ?? null, fn ($query, $hasta) => $query->whereDate('at.fecha', '<=', $hasta))
            ->whereNotExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('control_recorrido as cr')
                    ->whereColumn('cr.asignacion_turno_id', 'at.id')
                    ->whereColumn('cr.ruta_parada_id', 'rp.id')
                    ->where('cr.estado', 'cumplido');
            });
    }

    /**
     * Paradas marcadas explícitamente como omitidas en el control de recorrido.
     *
     * @param  array<string, mixed>  $filtros
     */
    private function consultaRegistradas(array $filtros)
    {
        return DB::table('control_recorrido as cr')
            ->join('asignacion_turnos as at', 'at.id', '=', 'cr.asignacion_turno_id')
            ->join('parada_ruta as rp', 'rp.id', '=', 'cr.ruta_parada_id')
            ->join('paradas as pa', 'pa.id', '=', 'rp.parada_id')
            ->join('ruta as r', 'r.id', '=', 'at.ruta_id')
            ->leftJoin('conductor as c', 'c.id', '=', 'at.conductor_id')
            ->leftJoin('users as uc', 'uc.id', '=', 'c.user_id')
            ->leftJoin('micro as m', 'm.id', '=', 'at.micro_id')
            ->leftJoin('interno as i', 'i.id', '=', 'm.interno_id')
            ->leftJoin('turno as t', 't.id', '=', 'at.turno_id')
            ->select($this->seleccion(['at.fecha as fecha', 'cr.fecha_hora as fecha_hora']))
            ->where('cr.estado', 'omitido')
            ->when($filtros['ruta_id'] ?? null, fn ($query, $rutaId) => $query->where('at.ruta_id', $rutaId))
            ->when($filtros['conductor_id'] ?? null, fn ($query, $conductorId) => $query->where('at.conductor_id', $conductorId))
            ->when($filtros['desde'] ?? null, fn ($query, $desde) => $query->whereDate('at.fecha', '>=', $desde))
            ->when($filtros['hasta'] ?? null, fn ($query, $hasta) => $query->whereDate('at.fecha', '<=', $hasta));
    }

    /**
     * Columnas comunes a ambas consultas.
     *
     * @param  array<int, mixed>  $fechas
     * @return array<int, mixed>
     */
    private function seleccion(array $fechas): array
    {
        return array_merge($fechas, [
            'at.id as asignacion_id',
            'rp.id as ruta_parada_id',
            't.nombre as turno_nombre',
            'r.nombre as ruta_nombre',
            'pa.nombre as parada_nombre',
            'rp.sentido as sentido',
            'rp.orden as orden',
            'm.placa as micro_placa',
            'i.numero_interno as interno_numero',
            'c.nombre as conductor_nombre',
            'c.apellido as conductor_apellido',
            'uc.name as conductor_user_nombre',
            'uc.apellido as conductor_user_apellido',
        ]);
    }

    private function clave(object $fila): string
    {
        return $fila->asignacion_id.'-'.$fila->ruta_parada_id;
    }

    /**
     * @return array<string, string>
     */
    private function formatear(object $fila): array
    {
        $nombre = trim((string) ($fila->conductor_user_nombre ?: $fila->conductor_nombre));
        $apellido = trim((string) ($fila->conductor_user_apellido ?: $fila->conductor_apellido));
        $conductor = trim($nombre.' '.$apellido);

        return [
            'fecha' => $fila->fecha ? Carbon::parse($fila->fecha)->format('d/m/Y') : '—',
            'hora' => $fila->fecha_hora ? Carbon::parse($fila->fecha_hora)->format('H:i') : '—',
            'turno' => ucfirst((string) $fila->turno_nombre) ?: '—',
            'conductor' => $conductor !== '' ? $conductor : 'Sin conductor',
            'ruta' => $fila->ruta_nombre ?: '—',
            'micro' => trim(($fila->micro_placa ?: 'S/P').' · Int. '.($fila->interno_numero ?: 'S/I')),
            'parada' => $fila->parada_nombre ?: '—',
            'sentido' => $fila->sentido ?: '—',
            'orden' => (string) $fila->orden,
            'origen' => $fila->origen,
        ];
    }
}
