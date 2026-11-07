<?php

namespace App\Http\Controllers;

use App\Models\AsignacionTurno;
use App\Models\Conductor;
use App\Models\Micro;
use App\Models\Ruta;
use App\Models\Turno;
use App\Services\AsignacionTurnoService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AsignacionTurnoController extends Controller
{
    public function __construct(protected AsignacionTurnoService $asignacionTurnoService) {}

    /**
     * Tablero interactivo y lista de asignaciones de turno (SDD Sección 30).
     */
    public function index(Request $request): View
    {
        $buscar = $request->input('buscar');
        $fecha = $request->input('fecha', now()->toDateString());

        // Estadísticas y asignaciones operativas del día
        $asignacionesDia = AsignacionTurno::where('fecha', $fecha)
            ->where('estado', '!=', 'cancelado')
            ->with(['turno', 'conductor.user', 'micro.interno', 'ruta'])
            ->get();

        $totalConductoresActivos = Conductor::where('estado', 'activo')->count();
        $conductoresOcupados = $asignacionesDia->pluck('conductor_id')->unique()->count();

        $stats = [
            'turnos_cubiertos' => $asignacionesDia->count(),
            'conductores_libres' => max(0, $totalConductoresActivos - $conductoresOcupados),
            'unidades_en_ruta' => $asignacionesDia->where('estado', 'en_curso')->count(),
        ];

        // Unidades con sus turnos del día para el Tablero visual (Gantt/Timeline)
        $turnosCatalogo = Turno::where('estado', 'activo')->orderBy('hora_inicio')->get();
        $micros = Micro::where('estado', 'activo')->with('interno')->orderBy('placa')->get();

        $unidadesTimeline = $micros->map(function ($micro) use ($asignacionesDia, $turnosCatalogo) {
            $asignacionesMicro = $asignacionesDia->where('micro_id', $micro->id);

            $turnosAsignados = $turnosCatalogo->mapWithKeys(function ($t) use ($asignacionesMicro) {
                $asig = $asignacionesMicro->firstWhere('turno_id', $t->id);

                return [$t->nombre => $asig];
            });

            return [
                'micro' => $micro,
                'turnos' => $turnosAsignados,
                'total_turnos' => $asignacionesMicro->count(),
            ];
        });

        // Paginado de asignaciones con filtro
        $asignaciones = AsignacionTurno::with([
            'turno',
            'conductor.user',
            'micro.interno',
            'ruta',
        ])
            ->noCanceladas()
            ->when($buscar, fn ($q) => $q->buscarPorConductor($buscar))
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(10);

        return view('asignacion_turno.index', compact(
            'asignaciones',
            'buscar',
            'fecha',
            'stats',
            'unidadesTimeline',
            'turnosCatalogo'
        ));
    }

    /**
     * Formulario para crear una nueva asignación.
     */
    public function create(): View
    {
        $turnos = Turno::where('estado', 'activo')->orderBy('nombre')->get();
        $rutas = Ruta::where('estado', 'activo')->orderBy('nombre')->get();
        $micros = Micro::where('estado', 'activo')->with('interno')->orderBy('placa')->get();
        $conductores = Conductor::where('estado', 'activo')->orderBy('nombre')->get();
        $asignacion = new AsignacionTurno;

        return view('asignacion_turno.create', compact(
            'asignacion',
            'turnos',
            'rutas',
            'micros',
            'conductores'
        ));
    }

    /**
     * Almacena una nueva asignación de turno.
     * SDD Sección 13 y 40: estado siempre 'pendiente' al crear;
     * hora_salida y hora_llegada son determinados por el flujo de ejecución.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'turno_id' => ['required', 'integer', 'exists:turno,id'],
            'ruta_id' => ['required', 'integer', 'exists:ruta,id'],
            'micro_id' => ['required', 'integer', 'exists:micro,id'],
            'conductor_id' => ['required', 'integer', 'exists:conductor,id'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->asignacionTurnoService->crear($data);

        return redirect()
            ->route('asignacion-turno.index')
            ->with('success', 'Asignación de turno creada correctamente.');
    }

    /**
     * Detalle de una asignación.
     */
    public function show(int $id): View
    {
        $asignacion = $this->asignacionDisponible($id)->with([
            'turno',
            'conductor',
            'micro.interno',
            'ruta',
        ])->firstOrFail();

        return view('asignacion_turno.show', compact('asignacion'));
    }

    /**
     * Formulario de edición.
     */
    public function edit(int $id): View
    {
        $asignacion = $this->asignacionDisponible($id)->firstOrFail();
        $turnos = Turno::where('estado', 'activo')->orderBy('nombre')->get();
        $rutas = Ruta::where('estado', 'activo')->orderBy('nombre')->get();
        $micros = Micro::where('estado', 'activo')->with('interno')->orderBy('placa')->get();
        $conductores = Conductor::where('estado', 'activo')->orderBy('nombre')->get();

        return view('asignacion_turno.edit', compact(
            'asignacion',
            'turnos',
            'rutas',
            'micros',
            'conductores'
        ));
    }

    /**
     * Actualiza una asignación de turno.
     * Panel admin conserva control de estado para supervisión operativa.
     * hora_llegada es determinada automáticamente por el flujo GPS (SDD Sección 27).
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $asignacion = $this->asignacionDisponible($id)->firstOrFail();

        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'turno_id' => ['required', 'integer', 'exists:turno,id'],
            'ruta_id' => ['required', 'integer', 'exists:ruta,id'],
            'micro_id' => ['required', 'integer', 'exists:micro,id'],
            'conductor_id' => ['required', 'integer', 'exists:conductor,id'],
            'hora_salida' => ['nullable', 'date_format:H:i'],
            'estado' => ['required', 'in:pendiente,en_curso,completado,retrasado,cancelado'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->asignacionTurnoService->actualizar($asignacion, $data);

        return redirect()
            ->route('asignacion-turno.index')
            ->with('success', 'Asignación de turno actualizada correctamente.');
    }

    /**
     * Desactivación lógica (estado = cancelado) de la asignación.
     */
    public function destroy(int $id): RedirectResponse
    {
        $asignacion = $this->asignacionDisponible($id)->firstOrFail();
        $this->asignacionTurnoService->cancelar($asignacion);

        return redirect()
            ->route('asignacion-turno.index')
            ->with('info', 'Asignación cancelada correctamente.');
    }

    private function asignacionDisponible(int $id): Builder
    {
        return AsignacionTurno::noCanceladas()->whereKey($id);
    }
}
