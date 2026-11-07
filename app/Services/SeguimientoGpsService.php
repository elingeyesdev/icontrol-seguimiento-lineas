<?php

namespace App\Services;

use App\Models\AsignacionTurno;
use App\Models\ControlRecorrido;
use App\Models\SeguimientoGps;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SeguimientoGpsService
{
    public function __construct(
        protected ControlRecorridoService $controlRecorridoService
    ) {}

    /**
     * Registra un único punto GPS y evalúa el recorrido.
     */
    public function registrarPunto(AsignacionTurno $asignacion, array $datos): array
    {
        $this->validarRangoTemporal($datos['fecha_hora_gps']);

        if (in_array($asignacion->estado, ['completado', 'cancelado'], true)) {
            throw ValidationException::withMessages([
                'asignacion_turno_id' => 'La asignación ya no acepta nuevos puntos GPS.',
            ]);
        }

        return DB::transaction(function () use ($asignacion, $datos) {
            // Instancia temporal para calcular proximidad
            $gpsTemp = new SeguimientoGps([
                'fecha_hora_gps' => $datos['fecha_hora_gps'],
                'latitud' => $datos['latitud'],
                'longitud' => $datos['longitud'],
                'velocidad' => $datos['velocidad'] ?? 0.0,
            ]);

            // 1. Evaluar si la ubicación coincide con la siguiente parada esperada
            $control = $this->controlRecorridoService->evaluarPunto($asignacion, $gpsTemp);

            // 2. Si no tocó una parada, asociar al control_recorrido general en ruta de la asignación
            if (! $control) {
                $control = ControlRecorrido::firstOrCreate(
                    ['asignacion_turno_id' => $asignacion->id, 'ruta_parada_id' => null],
                    [
                        'fecha_hora' => $datos['fecha_hora_gps'],
                        'estado' => 'en_curso',
                        'observacion' => 'Seguimiento en ruta',
                    ]
                );
            }

            // 3. Registrar el punto GPS apuntando estrictamente a su control_recorrido padre
            $gps = SeguimientoGps::firstOrCreate(
                [
                    'control_recorrido_id' => $control->id,
                    'fecha_hora_gps' => $datos['fecha_hora_gps'],
                ],
                [
                    'latitud' => $datos['latitud'],
                    'longitud' => $datos['longitud'],
                    'velocidad' => $datos['velocidad'] ?? 0.0,
                    'fecha_hora_sincronizacion' => now(),
                ]
            );

            return [
                'seguimiento_gps' => $gps,
                'control_recorrido' => $control,
                'asignacion_estado' => $asignacion->fresh()->estado,
            ];
        });
    }

    /**
     * Registra un lote de ubicaciones sincronizadas offline (SDD Sección 17 y 26.5).
     */
    public function registrarLote(AsignacionTurno $asignacion, array $puntos): array
    {
        if (count($puntos) > 300) {
            throw ValidationException::withMessages([
                'ubicaciones' => 'El lote no puede exceder los 300 puntos por sincronización.',
            ]);
        }

        $guardados = 0;
        $duplicados = 0;
        $rechazados = [];

        foreach ($puntos as $index => $punto) {
            try {
                if (empty($punto['fecha_hora_gps']) || ! isset($punto['latitud']) || ! isset($punto['longitud'])) {
                    $rechazados[] = ['index' => $index, 'motivo' => 'Campos obligatorios faltantes'];

                    continue;
                }

                $this->validarRangoTemporal($punto['fecha_hora_gps']);

                $resultado = $this->registrarPunto($asignacion, $punto);

                if ($resultado['seguimiento_gps']->wasRecentlyCreated) {
                    $guardados++;
                } else {
                    $duplicados++;
                }
            } catch (\Throwable $e) {
                $rechazados[] = ['index' => $index, 'motivo' => $e->getMessage()];
            }
        }

        return [
            'total_procesados' => count($puntos),
            'guardados' => $guardados,
            'duplicados_omitidos' => $duplicados,
            'rechazados' => $rechazados,
            'asignacion_estado' => $asignacion->fresh()->estado,
        ];
    }

    /**
     * Devuelve la flota activa con su última posición GPS conocida.
     *
     * Este servicio es el único dueño del canal de seguimiento: no calcula
     * cumplimiento de paradas, solo entrega posiciones y datos de la unidad.
     *
     * @return array{timestamp: string, total_activas: int, unidades: array<int, array<string, mixed>>}
     */
    public function flotaEnVivo(): array
    {
        $asignaciones = AsignacionTurno::whereIn('estado', ['en_curso', 'pendiente', 'retrasado'])
            ->where('fecha', now()->toDateString())
            ->with([
                'turno',
                'conductor.user',
                'micro.interno',
                'ruta',
                'seguimientosGps' => fn ($query) => $query->latest('fecha_hora_gps')->take(1),
            ])
            ->orderBy('id')
            ->get();

        $unidades = $asignaciones->map(function (AsignacionTurno $asignacion) {
            $ultimoGps = $asignacion->seguimientosGps->first();

            return [
                'asignacion_id' => $asignacion->id,
                'placa' => $asignacion->micro->placa ?? 'S/P',
                'interno' => $asignacion->micro->interno->numero_interno ?? 'S/I',
                'conductor_id' => $asignacion->conductor?->id,
                'conductor' => $asignacion->conductor?->nombreCompleto() ?? 'Sin conductor',
                'ruta' => $asignacion->ruta->nombre ?? 'Sin ruta',
                'turno' => ucfirst($asignacion->turno->nombre ?? ''),
                'estado' => $asignacion->estado,
                'hora_salida' => $asignacion->hora_salida,
                'latitud' => $ultimoGps ? (float) $ultimoGps->latitud : null,
                'longitud' => $ultimoGps ? (float) $ultimoGps->longitud : null,
                'velocidad' => $ultimoGps ? (float) $ultimoGps->velocidad : 0.0,
                'ultima_actualizacion' => $ultimoGps ? $ultimoGps->fecha_hora_gps->format('H:i:s') : 'Sin reporte',
            ];
        })->values();

        return [
            'timestamp' => now()->toIso8601String(),
            'total_activas' => $unidades->count(),
            'unidades' => $unidades->all(),
        ];
    }

    /**
     * Valida que la fecha/hora del GPS no esté en el futuro más allá de un margen de 2 minutos.
     */
    protected function validarRangoTemporal(string|Carbon $fechaHoraGps): void
    {
        $fecha = Carbon::parse($fechaHoraGps);
        $limiteFuturo = now()->addMinutes(2);

        if ($fecha->greaterThan($limiteFuturo)) {
            throw ValidationException::withMessages([
                'fecha_hora_gps' => 'La fecha/hora del GPS no puede ser posterior a la hora actual del servidor.',
            ]);
        }
    }
}
