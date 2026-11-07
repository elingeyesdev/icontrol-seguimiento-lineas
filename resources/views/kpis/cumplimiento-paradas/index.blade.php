@extends('layouts.app')

@section('content')
@vite('resources/css/kpis/cumplimiento-paradas.css')

<div class="container-fluid py-4">
    <div class="page-header">
        <div>
            <h1 class="page-title"><i class="bi bi-check2-circle" aria-hidden="true"></i> Cumplimiento de paradas</h1>
            <p class="page-subtitle">Paradas reconocidas por el control de recorrido.</p>
        </div>
        <div class="text-muted small">Actualizado: {{ $actualizado->format('d/m/Y H:i') }}</div>
    </div>

    @include('kpis.cumplimiento-paradas.partials.filtros')

    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-4"><div class="card kpi-icp-card h-100"><div class="card-body"><div class="kpi-label">Porcentaje de cumplimiento</div>@if($indicadores['porcentaje'] !== null)<div class="kpi-value">{{ number_format($indicadores['porcentaje'], 1) }}%</div>@else<div class="kpi-empty">No hay paradas programadas.</div>@endif</div></div></div>
        <div class="col-12 col-lg-4"><div class="card kpi-icp-card h-100"><div class="card-body"><div class="kpi-label">Paradas cumplidas</div><div class="kpi-value">{{ $indicadores['cumplidas'] }}</div></div></div></div>
        <div class="col-12 col-lg-4"><div class="card kpi-icp-card h-100"><div class="card-body"><div class="kpi-label">Paradas programadas</div><div class="kpi-value">{{ $indicadores['programadas'] }}</div></div></div></div>
    </div>

    <div class="row g-3">
        @foreach(['evolucion' => 'Evolución por fecha', 'por_ruta' => 'Desglose por ruta', 'por_conductor' => 'Desglose por conductor', 'por_micro' => 'Desglose por micro', 'por_turno' => 'Desglose por turno'] as $clave => $titulo)
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-header fw-semibold">{{ $titulo }}</div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead><tr><th>{{ $clave === 'evolucion' ? 'Fecha' : 'Nombre' }}</th><th>Cumplidas</th><th>Programadas</th><th class="text-end">Porcentaje</th></tr></thead>
                            <tbody>
                                @forelse($indicadores[$clave] as $fila)
                                    <tr><td>{{ $clave === 'evolucion' ? \Carbon\Carbon::parse($fila['nombre'])->format('d/m/Y') : $fila['nombre'] }}</td><td>{{ $fila['cumplidas'] }}</td><td>{{ $fila['programadas'] }}</td><td class="text-end">{{ $fila['porcentaje'] ?? 0 }}%</td></tr>
                                @empty
                                    <tr><td colspan="4" class="text-muted">Sin datos.</td></tr>
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
