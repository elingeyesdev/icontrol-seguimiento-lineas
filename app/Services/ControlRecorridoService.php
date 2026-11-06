<?php

namespace App\Services;

use App\Models\AsignacionTurno;
use App\Models\ControlRecorrido;
use App\Models\Ruta;
use App\Models\RutaParada;
use App\Models\SeguimientoGps;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ControlRecorridoService
{
    /**
     * Radio de tolerancia en metros para dar por cumplida una parada.
     */
    public const RADIO_TOLERANCIA_METROS = 80.0;

    /**
     * Genera el itinerario planificado de una asignación de turno.
     *
     * Cada parada activa de la ruta (ida y vuelta) se registra una sola vez
     * en estado pendiente. Es idempotente para no duplicar filas.
     */
    public function planificar(AsignacionTurno $asignacion): void
    {
        $ruta = $asignacion->ruta;

        if (! $ruta) {
            return;
        }

        $this->paradasDeRuta($ruta)->each(function (RutaParada $rutaParada) use ($asignacion) {
            ControlRecorrido::firstOrCreate(
                [
                    'asignacion_turno_id' => $asignacion->id,
                    'ruta_parada_id' => $rutaParada->id,
                ],
                [
                    'fecha_hora' => null,
                    'estado' => 'pendiente',
                    'observacion' => 'Parada planificada',
                ]
            );
        });
    }

    /**
     * Evalúa una posición GPS contra la secuencia planificada.
     *
     * El recorrido avanza solo hacia adelante. Si la unidad aparece dentro del
     * radio de una parada posterior, las paradas intermedias se marcan como
     * omitidas y la detectada se marca como cumplida.
     */
    public function evaluarPunto(AsignacionTurno $asignacion, SeguimientoGps $gps): ?ControlRecorrido
    {
        $ruta = $asignacion->ruta;

        if (! $ruta) {
            return null;
        }

        $paradasIda = $this->paradasDeRuta($ruta, 'Ida');
        $paradasVuelta = $this->paradasDeRuta($ruta, 'Vuelta');

        if ($paradasIda->isEmpty() && $paradasVuelta->isEmpty()) {
            return null;
        }

        $controles = $this->controlesDeRuta($asignacion);
        $secuencia = $this->secuenciaActiva($paradasIda, $paradasVuelta, $controles);

        if (! $secuencia) {
            return null;
        }

        [$sentidoActivo, $paradas] = $secuencia;

        $pendientes = $paradas
            ->filter(fn (RutaParada $rutaParada) => ! $this->estaResuelta($controles->get($rutaParada->id)))
            ->values();

        foreach ($pendientes as $indice => $rutaParada) {
            if (! $rutaParada->parada) {
                continue;
            }

            $distancia = $this->calcularDistanciaMetros(
                (float) $gps->latitud,
                (float) $gps->longitud,
                (float) $rutaParada->parada->latitud,
                (float) $rutaParada->parada->longitud
            );

            if ($distancia > self::RADIO_TOLERANCIA_METROS) {
                continue;
            }

            // Toda parada anterior pendiente se considera omitida.
            $pendientes
                ->take($indice)
                ->each(fn (RutaParada $omitida) => $this->marcarOmitida($asignacion, $omitida, $gps, $rutaParada));

            $control = $this->marcarCumplida($asignacion, $rutaParada, $gps, $sentidoActivo, $distancia);

            $this->finalizarSiCorresponde($asignacion, $rutaParada, $paradasIda, $paradasVuelta, $sentidoActivo);

            return $control;
        }

        return null;
    }

    /**
     * Resumen de cumplimiento de una asignación.
     *
     * @return array
     *     total: int,
     *     cumplidas: int,
     *     omitidas: int,
     *     pendientes: int,
     *     porcentaje: int,
     *     sentido: string,
     *     siguiente_parada: RutaParada|null
     * }
     */
    public function resumen(AsignacionTurno $asignacion): array
    {
        $ruta = $asignacion->ruta;
        $paradas = $ruta ? $this->paradasDeRuta($ruta) : collect();
        $controles = $this->controlesDeRuta($asignacion);

        $cumplidas = 0;
        $omitidas = 0;

        foreach ($paradas as $rutaParada) {
            $estado = $controles->get($rutaParada->id)?->estado;

            if ($estado === 'cumplido') {
                $cumplidas++;
            } elseif ($estado === 'omitido') {
                $omitidas++;
            }
        }

        $total = $paradas->count();
        $secuencia = $this->secuenciaActiva(
            $paradas->where('sentido', 'Ida')->values(),
            $paradas->where('sentido', 'Vuelta')->values(),
            $controles
        );

        $siguienteParada = $secuencia
            ? $secuencia[1]->first(fn (RutaParada $rutaParada) => ! $this->estaResuelta($controles->get($rutaParada->id)))
            : null;

        return [
            'total' => $total,
            'cumplidas' => $cumplidas,
            'omitidas' => $omitidas,
            'pendientes' => max(0, $total - $cumplidas - $omitidas),
            'porcentaje' => $total > 0 ? (int) round(($cumplidas / $total) * 100) : 0,
            'sentido' => $secuencia[0] ?? ($paradas->contains('sentido', 'Vuelta') ? 'Vuelta' : 'Ida'),
            'siguiente_parada' => $siguienteParada,
        ];
    }

    /**
     * Calcula la distancia Haversine en metros entre dos coordenadas geográficas.
     */
    public function calcularDistanciaMetros(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // Radio de la Tierra en metros

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Obtiene las paradas activas de una ruta, opcionalmente filtradas por sentido.
     *
     * @return Collection<int, RutaParada>
     */
    protected function paradasDeRuta(Ruta $ruta, ?string $sentido = null): Collection
    {
        $paradas = $ruta->relationLoaded('rutaParadas')
            ? $ruta->rutaParadas->where('estado', 'activo')
            : RutaParada::where('ruta_id', $ruta->id)
                ->where('estado', 'activo')
                ->with('parada')
                ->get();

        if ($sentido !== null) {
            $paradas = $paradas->where('sentido', $sentido);
        }

        return $paradas
            ->sortBy([
                ['sentido', 'asc'],
                ['orden', 'asc'],
            ])
            ->values();
    }

    /**
     * Controles de parada de la asignación, indexados por ruta_parada_id.
     *
     * Si existieran duplicados históricos, se prioriza el estado resuelto.
     *
     * @return Collection<int, ControlRecorrido>
     */
    protected function controlesDeRuta(AsignacionTurno $asignacion): Collection
    {
        $controles = $asignacion->relationLoaded('controlesRecorrido')
            ? $asignacion->controlesRecorrido
            : $asignacion->controlesRecorrido()->get();

        return $controles
            ->whereNotNull('ruta_parada_id')
            ->groupBy('ruta_parada_id')
            ->map(fn (Collection $grupo) => $grupo
                ->sortBy(fn (ControlRecorrido $control) => match ($control->estado) {
                    'cumplido' => 0,
                    'omitido' => 1,
                    default => 2,
                })
                ->first());
    }

    /**
     * Determina la secuencia activa (Ida o Vuelta) según las paradas resueltas.
     *
     * @param  Collection<int, RutaParada>  $paradasIda
     * @param  Collection<int, RutaParada>  $paradasVuelta
     * @param  Collection<int, ControlRecorrido>  $controles
     * @return array{0: string, 1: Collection<int, RutaParada>}|null
     */
    protected function secuenciaActiva(Collection $paradasIda, Collection $paradasVuelta, Collection $controles): ?array
    {
        if ($paradasIda->isNotEmpty() && $paradasIda->contains(
            fn (RutaParada $rutaParada) => ! $this->estaResuelta($controles->get($rutaParada->id))
        )) {
            return ['Ida', $paradasIda];
        }

        if ($paradasVuelta->isNotEmpty() && $paradasVuelta->contains(
            fn (RutaParada $rutaParada) => ! $this->estaResuelta($controles->get($rutaParada->id))
        )) {
            return ['Vuelta', $paradasVuelta];
        }

        return null;
    }

    /**
     * Indica si una parada ya fue cumplida u omitida.
     */
    protected function estaResuelta(?ControlRecorrido $control): bool
    {
        return in_array($control?->estado, ['cumplido', 'omitido'], true);
    }

    /**
     * Marca una parada como omitida porque se detectó una parada posterior.
     */
    protected function marcarOmitida(
        AsignacionTurno $asignacion,
        RutaParada $rutaParada,
        SeguimientoGps $gps,
        RutaParada $paradaDetectada
    ): void {
        $control = ControlRecorrido::firstOrCreate(
            [
                'asignacion_turno_id' => $asignacion->id,
                'ruta_parada_id' => $rutaParada->id,
            ],
            [
                'fecha_hora' => null,
                'estado' => 'pendiente',
                'observacion' => 'Parada planificada',
            ]
        );

        if ($this->estaResuelta($control)) {
            return;
        }

        $control->update([
            'fecha_hora' => $gps->fecha_hora_gps,
            'estado' => 'omitido',
            'distancia_metros' => null,
            'observacion' => "Parada omitida: se detectó paso por '{$paradaDetectada->parada?->nombre}' sin registrar esta parada.",
        ]);
    }

    /**
     * Marca una parada como cumplida y calcula la distancia registrada.
     */
    protected function marcarCumplida(
        AsignacionTurno $asignacion,
        RutaParada $rutaParada,
        SeguimientoGps $gps,
        string $sentidoActivo,
        float $distancia
    ): ControlRecorrido {
        $control = ControlRecorrido::firstOrCreate(
            [
                'asignacion_turno_id' => $asignacion->id,
                'ruta_parada_id' => $rutaParada->id,
            ],
            [
                'fecha_hora' => null,
                'estado' => 'pendiente',
                'observacion' => 'Parada planificada',
            ]
        );

        $control->update([
            'fecha_hora' => $gps->fecha_hora_gps,
            'estado' => 'cumplido',
            'distancia_metros' => round($distancia, 2),
            'observacion' => "Parada cumplida [{$sentidoActivo}]: {$rutaParada->parada?->nombre} a ".round($distancia, 2).'m',
        ]);

        return $control;
    }

    /**
     * Cierra la asignación al cumplir la última parada del recorrido.
     *
     * @param  Collection<int, RutaParada>  $paradasIda
     * @param  Collection<int, RutaParada>  $paradasVuelta
     */
    protected function finalizarSiCorresponde(
        AsignacionTurno $asignacion,
        RutaParada $rutaParada,
        Collection $paradasIda,
        Collection $paradasVuelta,
        string $sentidoActivo
    ): void {
        $ultimaParada = $paradasVuelta->isNotEmpty()
            ? $paradasVuelta->last()
            : $paradasIda->last();

        if ($rutaParada->id !== $ultimaParada?->id || $asignacion->estado !== 'en_curso') {
            return;
        }

        $asignacion->update([
            'estado' => 'completado',
            'hora_llegada' => now()->format('H:i:s'),
        ]);

        Log::info("Asignacion #{$asignacion->id} culminada automáticamente al cumplir la última parada [{$sentidoActivo}].");
    }
}
