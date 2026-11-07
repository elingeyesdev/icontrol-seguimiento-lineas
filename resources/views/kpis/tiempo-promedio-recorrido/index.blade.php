@extends('layouts.app')

@section('content')
@vite('resources/css/kpis/tiempo-promedio-recorrido.css')

<div class="container-fluid py-4">
    <div class="page-header">
        <div>
            <h1 class="page-title"><i class="bi bi-stopwatch" aria-hidden="true"></i> Tiempo promedio de recorrido</h1>
            <p class="page-subtitle">Recorridos finalizados y válidos del periodo consultado.</p>
        </div>
        <div class="text-muted small">Actualizado: {{ $actualizado->format('d/m/Y H:i') }}</div>
    </div>

    @include('kpis.tiempo-promedio-recorrido.partials.filtros')

    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-6">
            <div class="card kpi-tpr-card h-100">
                <div class="card-body">
                    <div class="kpi-label">Tiempo promedio</div>
                    @if($indicadores['promedio'] !== null)
                        <div class="kpi-value">{{ floor($indicadores['promedio'] / 60) }} h {{ (int) $indicadores['promedio'] % 60 }} min</div>
                    @else
                        <div class="kpi-empty">No hay recorridos válidos.</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card kpi-tpr-card h-100">
                <div class="card-body">
                    <div class="kpi-label">Recorridos considerados</div>
                    <div class="kpi-value">{{ $indicadores['recorridos'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-xl-6">
            <div class="card h-100">
                <div class="card-header fw-semibold">Evolución por fecha (minutos)</div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead><tr><th>Fecha</th><th>Recorridos</th><th class="text-end">Promedio</th></tr></thead>
                        <tbody>
                            @forelse($indicadores['evolucion'] as $fila)
                                <tr><td>{{ \Carbon\Carbon::parse($fila['nombre'])->format('d/m/Y') }}</td><td>{{ $fila['recorridos'] }}</td><td class="text-end">{{ $fila['promedio'] }} min</td></tr>
                            @empty
                                <tr><td colspan="3" class="text-muted">Sin datos.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="card h-100">
                <div class="card-header fw-semibold">Desglose por ruta (minutos)</div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead><tr><th>Ruta</th><th>Recorridos</th><th class="text-end">Promedio</th></tr></thead>
                        <tbody>
                            @forelse($indicadores['por_ruta'] as $fila)
                                <tr><td>{{ $fila['nombre'] }}</td><td>{{ $fila['recorridos'] }}</td><td class="text-end">{{ $fila['promedio'] }} min</td></tr>
                            @empty
                                <tr><td colspan="3" class="text-muted">Sin datos.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @foreach(['por_conductor' => 'conductor', 'por_micro' => 'micro', 'por_turno' => 'turno'] as $clave => $titulo)
            <div class="col-12 col-lg-4">
                <div class="card h-100">
                    <div class="card-header fw-semibold">Desglose por {{ $titulo }} (minutos)</div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead><tr><th>Nombre</th><th class="text-end">Promedio</th></tr></thead>
                            <tbody>
                                @forelse($indicadores[$clave] as $fila)
                                    <tr><td>{{ $fila['nombre'] }}</td><td class="text-end">{{ $fila['promedio'] }} min</td></tr>
                                @empty
                                    <tr><td colspan="2" class="text-muted">Sin datos.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
