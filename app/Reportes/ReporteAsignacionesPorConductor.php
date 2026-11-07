<?php

namespace App\Reportes;

use App\Models\Conductor;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Reporte: Asignaciones de Turnos por Conductor (mes y año).
 *
 * Lista las asignaciones registradas en un periodo mensual, opcionalmente
 * filtradas por conductor y estado, mostrando ruta, unidad e internos.
 */
class ReporteAsignacionesPorConductor extends ReporteBase
{
    /**
     * Etiquetas legibles de los estados de una asignación.
     */
    private const ESTADOS = [
        'pendiente' => 'Pendiente',
        'en_curso' => 'En curso',
        'completado' => 'Completado',
        'retrasado' => 'Retrasado',
        'cancelado' => 'Cancelado',
    ];

    /**
     * Etiquetas legibles de los turnos del catálogo.
     */
    private const TURNOS = [
        'mañana' => 'Mañana',
        'tarde' => 'Tarde',
        'noche' => 'Noche',
    ];

    public function titulo(): string
    {
        return 'Asignaciones de Turnos por Conductor';
    }

    public function descripcion(): string
    {
        return 'Listado mensual de asignaciones de turno por conductor, con ruta, unidad asignada y estado operativo.';
    }

    public function columnas(): array
    {
        return [
            'codigo' => 'Código',
            'fecha' => 'Fecha',
            'turno' => 'Turno',
            'conductor' => 'Conductor',
            'ruta' => 'Ruta',
            'micro' => 'Micro / Interno',
            'salida' => 'Hora salida',
            'llegada' => 'Hora llegada',
            'estado' => 'Estado',
        ];
    }

    public function resultados(array $filtros): Collection
    {
        $anio = (int) ($filtros['anio'] ?? now()->year);
        $mes = (int) ($filtros['mes'] ?? now()->month);
        $conductorId = $filtros['conductor_id'] ?? null;
        $estado = $filtros['estado'] ?? null;

        return DB::table('asignacion_turnos as at')
            ->leftJoin('conductor as c', 'c.id', '=', 'at.conductor_id')
            ->leftJoin('users as uc', 'uc.id', '=', 'c.user_id')
            ->leftJoin('ruta as r', 'r.id', '=', 'at.ruta_id')
            ->leftJoin('micro as m', 'm.id', '=', 'at.micro_id')
            ->leftJoin('interno as i', 'i.id', '=', 'm.interno_id')
            ->leftJoin('turno as t', 't.id', '=', 'at.turno_id')
            ->select([
                'at.id as asignacion_id',
                'at.fecha',
                'at.hora_salida',
                'at.hora_llegada',
                'at.estado',
                't.nombre as turno_nombre',
                'r.nombre as ruta_nombre',
                'm.placa as micro_placa',
                'i.numero_interno as interno_numero',
                'c.nombre as conductor_nombre',
                'c.apellido as conductor_apellido',
                'uc.name as conductor_user_nombre',
                'uc.apellido as conductor_user_apellido',
            ])
            ->whereYear('at.fecha', $anio)
            ->whereMonth('at.fecha', $mes)
            ->when($conductorId, fn ($query) => $query->where('at.conductor_id', $conductorId))
            ->when($estado, fn ($query) => $query->where('at.estado', $estado))
            ->orderBy('at.fecha')
            ->orderBy('at.id')
            ->get()
            ->map(fn (object $fila) => $this->formatear($fila));
    }

    public function resumen(Collection $filas): array
    {
        return [
            'Total de asignaciones' => $filas->count(),
            'Completadas' => $filas->where('estado', 'Completado')->count(),
            'En curso / Pendientes' => $filas->whereIn('estado', ['En curso', 'Pendiente'])->count(),
            'Canceladas' => $filas->where('estado', 'Cancelado')->count(),
            'Conductores' => $filas->pluck('conductor')->unique()->count(),
        ];
    }

    public function filtrosAplicados(array $filtros): array
    {
        $conductorId = $filtros['conductor_id'] ?? null;
        $conductor = $conductorId ? Conductor::with('user')->find($conductorId) : null;

        return array_filter([
            'Mes' => Carbon::createFromDate((int) ($filtros['anio'] ?? now()->year), (int) ($filtros['mes'] ?? now()->month), 1)->translatedFormat('F'),
            'Año' => (string) ($filtros['anio'] ?? now()->year),
            'Conductor' => $conductor?->nombre_completo,
            'Estado' => self::ESTADOS[$filtros['estado'] ?? ''] ?? null,
        ], fn ($valor) => $valor !== null && $valor !== '');
    }

    public function opcionesFiltros(): array
    {
        return [
            'conductores' => Conductor::with('user')->orderBy('nombre')->get(),
            'estados' => self::ESTADOS,
        ];
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
            'codigo' => 'AT-'.str_pad((string) $fila->asignacion_id, 4, '0', STR_PAD_LEFT),
            'fecha' => $fila->fecha ? Carbon::parse($fila->fecha)->format('d/m/Y') : '—',
            'turno' => self::TURNOS[$fila->turno_nombre] ?? ucfirst((string) $fila->turno_nombre),
            'conductor' => $conductor !== '' ? $conductor : 'Sin conductor',
            'ruta' => $fila->ruta_nombre ?: '—',
            'micro' => trim(($fila->micro_placa ?: 'S/P').' · Int. '.($fila->interno_numero ?: 'S/I')),
            'salida' => $this->hora($fila->hora_salida),
            'llegada' => $this->hora($fila->hora_llegada),
            'estado' => self::ESTADOS[$fila->estado] ?? ucfirst((string) $fila->estado),
        ];
    }

    private function hora(?string $hora): string
    {
        return $hora ? substr($hora, 0, 5) : '—';
    }
}
