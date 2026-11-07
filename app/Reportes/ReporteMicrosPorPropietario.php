<?php

namespace App\Reportes;

use App\Models\Interno;
use App\Models\Propietario;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Reporte: Micros por Propietario e Interno Asignado.
 *
 * Lista la flota registrada relacionando cada micro con su propietario y el
 * interno (número) que tiene asignado, con filtros por propietario e interno.
 */
class ReporteMicrosPorPropietario extends ReporteBase
{
    /**
     * Etiquetas legibles del estado de un micro.
     */
    private const ESTADOS = [
        'activo' => 'Activo',
        'inactivo' => 'Inactivo',
    ];

    public function titulo(): string
    {
        return 'Micros por Propietario e Interno Asignado';
    }

    public function descripcion(): string
    {
        return 'Listado de la flota agrupada por propietario, indicando el interno asignado, características y estado de cada unidad.';
    }

    public function columnas(): array
    {
        return [
            'placa' => 'Placa',
            'vehiculo' => 'Vehículo',
            'propietario' => 'Propietario',
            'interno' => 'Interno asignado',
            'capacidad' => 'Capacidad',
            'estado' => 'Estado',
        ];
    }

    public function resultados(array $filtros): Collection
    {
        $propietarioId = $filtros['propietario_id'] ?? null;
        $internoId = $filtros['interno_id'] ?? null;
        $estado = $filtros['estado'] ?? null;
        $buscar = $filtros['buscar'] ?? null;

        return DB::table('micro as m')
            ->leftJoin('propietarios as p', 'p.id', '=', 'm.propietario_id')
            ->leftJoin('users as up', 'up.id', '=', 'p.user_id')
            ->leftJoin('interno as i', 'i.id', '=', 'm.interno_id')
            ->select([
                'm.id as micro_id',
                'm.placa',
                'm.marca',
                'm.modelo',
                'm.anio_fabricacion',
                'm.capacidad_pasajeros',
                'm.estado',
                'p.nombre as propietario_nombre',
                'p.apellido as propietario_apellido',
                'up.name as propietario_user_nombre',
                'up.apellido as propietario_user_apellido',
                'i.numero_interno as interno_numero',
            ])
            ->when($propietarioId, fn ($query) => $query->where('m.propietario_id', $propietarioId))
            ->when($internoId, fn ($query) => $query->where('m.interno_id', $internoId))
            ->when($estado, fn ($query) => $query->where('m.estado', $estado))
            ->when($buscar, function ($query, $buscar) {
                return $query->where(function ($sub) use ($buscar) {
                    $sub->where('m.placa', 'like', "%{$buscar}%")
                        ->orWhere('m.marca', 'like', "%{$buscar}%")
                        ->orWhere('m.modelo', 'like', "%{$buscar}%")
                        ->orWhere('i.numero_interno', 'like', "%{$buscar}%")
                        ->orWhere('p.nombre', 'like', "%{$buscar}%")
                        ->orWhere('p.apellido', 'like', "%{$buscar}%");
                });
            })
            ->orderBy('propietario_nombre')
            ->orderBy('m.placa')
            ->get()
            ->map(fn (object $fila) => $this->formatear($fila));
    }

    public function resumen(Collection $filas): array
    {
        return [
            'Total de micros' => $filas->count(),
            'Activos' => $filas->where('estado', 'Activo')->count(),
            'Inactivos' => $filas->where('estado', 'Inactivo')->count(),
            'Con interno asignado' => $filas->where('interno', '!=', 'Sin interno')->count(),
            'Propietarios' => $filas->pluck('propietario')->unique()->count(),
        ];
    }

    public function filtrosAplicados(array $filtros): array
    {
        $propietarioId = $filtros['propietario_id'] ?? null;
        $internoId = $filtros['interno_id'] ?? null;

        $propietario = $propietarioId ? Propietario::with('user')->find($propietarioId) : null;
        $interno = $internoId ? Interno::find($internoId) : null;

        return array_filter([
            'Propietario' => $propietario ? trim($propietario->nombre.' '.$propietario->apellido) : null,
            'Interno' => $interno?->numero_interno,
            'Estado' => self::ESTADOS[$filtros['estado'] ?? ''] ?? null,
            'Búsqueda' => $filtros['buscar'] ?? null,
        ], fn ($valor) => $valor !== null && $valor !== '');
    }

    public function opcionesFiltros(): array
    {
        return [
            'propietarios' => Propietario::with('user')->orderBy('nombre')->get(),
            'internos' => Interno::orderBy('numero_interno')->get(),
            'estados' => self::ESTADOS,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function formatear(object $fila): array
    {
        $nombre = trim((string) ($fila->propietario_user_nombre ?: $fila->propietario_nombre));
        $apellido = trim((string) ($fila->propietario_user_apellido ?: $fila->propietario_apellido));
        $propietario = trim($nombre.' '.$apellido);
        $vehiculo = trim(($fila->marca ?: 'S/M').' '.($fila->modelo ?: ''));

        return [
            'placa' => $fila->placa ?: 'S/P',
            'vehiculo' => $fila->anio_fabricacion ? "{$vehiculo} ({$fila->anio_fabricacion})" : $vehiculo,
            'propietario' => $propietario !== '' ? $propietario : 'Sin propietario',
            'interno' => $fila->interno_numero ?: 'Sin interno',
            'capacidad' => $fila->capacidad_pasajeros ? $fila->capacidad_pasajeros.' pax' : '—',
            'estado' => self::ESTADOS[$fila->estado] ?? ucfirst((string) $fila->estado),
        ];
    }
}
