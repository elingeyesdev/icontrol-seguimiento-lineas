<?php

namespace App\Http\Controllers\Kpis;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kpis\TiempoPromedioRecorridoRequest;
use App\Models\Conductor;
use App\Models\Micro;
use App\Models\Ruta;
use App\Models\Turno;
use App\Services\Kpis\TiempoPromedioRecorridoService;
use Illuminate\View\View;

class TiempoPromedioRecorridoController extends Controller
{
    public function __construct(private readonly TiempoPromedioRecorridoService $service) {}

    public function index(TiempoPromedioRecorridoRequest $request): View
    {
        return view('kpis.tiempo-promedio-recorrido.index', [
            ...$this->catalogos($request),
            'filtros' => $request->validated(),
            'indicadores' => $this->service->obtenerIndicadores($request->validated(), $request->user()),
            'actualizado' => now(),
        ]);
    }

    private function catalogos(TiempoPromedioRecorridoRequest $request): array
    {
        return [
            'rutas' => Ruta::where('estado', 'activo')->orderBy('nombre')->get(['id', 'nombre']),
            'conductores' => Conductor::where('estado', 'activo')->orderBy('nombre')->get(['id', 'nombre', 'apellido']),
            'micros' => Micro::where('estado', 'activo')->when($request->user()->hasRole('propietario'), fn ($q) => $q->where('propietario_id', $request->user()->propietario?->id ?? 0))->orderBy('placa')->get(['id', 'placa']),
            'turnos' => Turno::where('estado', 'activo')->orderBy('nombre')->get(['id', 'nombre']),
        ];
    }
}
