<?php

namespace App\Http\Controllers;

use App\Models\AsignacionTurno;
use App\Models\ControlRecorrido;
use App\Models\Ruta;
use App\Models\RutaParada;
use App\Services\ControlRecorridoService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ControlRecorridoController extends Controller
{
    public function __construct(
        protected ControlRecorridoService $controlService
    ) {}

    /**
     * Pantalla 1: Listado de asignaciones con control de recorrido.
     */
    public function index(Request $request): View
    {
        $fecha = $request->input('fecha', now()->toDateString());
        $rutaId = $request->input('ruta_id');
        $estado = $request->input('estado');
        $buscar = trim((string) $request->input('buscar', ''));

        $query = AsignacionTurno::where('fecha', $fecha)
            ->where('estado', '!=', 'cancelado')
            ->when($rutaId, fn ($q) => $q->where('ruta_id', $rutaId))
            ->when($estado, fn ($q) => $q->where('estado', $estado))
            ->when($buscar !== '', fn ($q) => $this->buscarAsignaciones($q, $buscar))
            ->with([
                'turno',
                'conductor.user',
                'micro.interno',
                'ruta',
            ])
            ->orderByDesc('id');

        $asignaciones = $query->paginate(10)->withQueryString();

        $asignaciones->each(function (AsignacionTurno $a) {
            $a->setAttribute('codigo', 'AT-'.str_pad($a->id, 3, '0', STR_PAD_LEFT));
        });

        $rutas = Ruta::where('estado', 'activo')->orderBy('nombre')->get();

        return view('control_recorrido.index', compact('asignaciones', 'rutas', 'fecha', 'rutaId', 'estado', 'buscar'));
    }

    /**
     * Pantalla 2: Detalle del recorrido con mapa y métricas en tiempo real.
     */
    public function show(int $id): View
    {
        $asignacion = AsignacionTurno::with([
            'turno',
            'conductor.user',
            'micro.interno',
            'ruta.rutaParadas' => fn ($q) => $q->where('estado', 'activo')->with('parada')->orderBy('sentido')->orderBy('orden'),
            'controlesRecorrido',
        ])->findOrFail($id);

        $paradasIda = $asignacion->ruta?->rutaParadas()->where('sentido', 'Ida')->where('estado', 'activo')->with('parada')->orderBy('orden')->get() ?? collect();
        $paradasVuelta = $asignacion->ruta?->rutaParadas()->where('sentido', 'Vuelta')->where('estado', 'activo')->with('parada')->orderBy('orden')->get() ?? collect();

        $resumen = $this->controlService->resumen($asignacion);
        $siguienteId = $resumen['siguiente_parada']?->id;

        $codigo = 'AT-'.str_pad($asignacion->id, 3, '0', STR_PAD_LEFT);

        return view('control_recorrido.show', [
            'asignacion' => $asignacion,
            'filasIda' => $this->filasRecorrido($asignacion, $paradasIda, $siguienteId),
            'filasVuelta' => $this->filasRecorrido($asignacion, $paradasVuelta, $siguienteId),
            'sentidoActivo' => $resumen['sentido'],
            'siguienteParada' => $resumen['siguiente_parada'],
            'totalParadas' => $resumen['total'],
            'paradasCumplidas' => $resumen['cumplidas'],
            'paradasOmitidas' => $resumen['omitidas'],
            'paradasPendientes' => $resumen['pendientes'],
            'porcentaje' => $resumen['porcentaje'],
            'codigo' => $codigo,
        ]);
    }

    /**
     * Filtra asignaciones por código, conductor, micro o ruta.
     *
     * @param  Builder<AsignacionTurno>  $query
     */
    protected function buscarAsignaciones(Builder $query, string $buscar): void
    {
        $query->where(function ($subquery) use ($buscar) {
            $termino = '%'.$buscar.'%';

            $subquery
                ->whereHas('conductor', fn ($conductor) => $conductor
                    ->where('nombre', 'like', $termino)
                    ->orWhere('apellido', 'like', $termino)
                    ->orWhere('licencia', 'like', $termino)
                    ->orWhere('ci', 'like', $termino))
                ->orWhereHas('conductor.user', fn ($user) => $user
                    ->where('name', 'like', $termino)
                    ->orWhere('apellido', 'like', $termino))
                ->orWhereHas('micro', fn ($micro) => $micro->where('placa', 'like', $termino))
                ->orWhereHas('micro.interno', fn ($interno) => $interno->where('numero_interno', 'like', $termino))
                ->orWhereHas('ruta', fn ($ruta) => $ruta->where('nombre', 'like', $termino));

            if (preg_match('/^(?:AT-?)?0*(\d+)$/i', $buscar, $coincidencia)) {
                $subquery->orWhere('id', (int) $coincidencia[1]);
            }
        });
    }

    /**
     * Filas de paradas con su estado real de paso.
     *
     * @param  Collection<int, RutaParada>  $paradas
     * @return Collection<int, array<string, mixed>>
     */
    protected function filasRecorrido(AsignacionTurno $asignacion, Collection $paradas, ?int $siguienteId = null): Collection
    {
        return $paradas->values()->map(function (RutaParada $rutaParada, int $index) use ($asignacion, $siguienteId) {
            $control = $this->controlDeParada($asignacion, $rutaParada->id);
            $estado = $control?->estado ?? 'pendiente';

            if ($estado === 'pendiente' && $siguienteId === $rutaParada->id) {
                $estado = 'en_proceso';
            }

            return [
                'numero' => $index + 1,
                'parada' => $rutaParada->parada,
                'ruta_parada' => $rutaParada,
                'sentido' => $rutaParada->sentido,
                'hora' => $control?->fecha_hora?->format('H:i'),
                'estado' => $estado,
                'distancia' => $control?->distancia_metros,
                'observacion' => $control?->observacion,
                'lat' => (float) ($rutaParada->parada?->latitud ?? 0),
                'lng' => (float) ($rutaParada->parada?->longitud ?? 0),
            ];
        });
    }

    /**
     * Control resuelto de una parada, priorizando el estado definitivo.
     */
    protected function controlDeParada(AsignacionTurno $asignacion, int $rutaParadaId): ?ControlRecorrido
    {
        return $asignacion->controlesRecorrido
            ->where('ruta_parada_id', $rutaParadaId)
            ->sortBy(fn (ControlRecorrido $control) => match ($control->estado) {
                'cumplido' => 0,
                'omitido' => 1,
                default => 2,
            })
            ->first();
    }
}
