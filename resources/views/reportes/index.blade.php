@extends('layouts.app')
@section('content')
@vite('resources/css/reportes.css')
@php
    $iconos = [
        'asignaciones-conductor' => 'bi-calendar-check',
        'micros-propietario' => 'bi-truck',
        'paradas-omitidas' => 'bi-signpost-split',
    ];
@endphp
<div class="container-fluid py-4">
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="bi bi-bar-chart-line" aria-hidden="true"></i>Módulo de Reportes
            </h1>
            <p class="page-subtitle">
                Consultas de asignaciones, flota y paradas omitidas. Cada reporte permite filtrar, exportar a PDF y descargar en formato Excel (CSV).
            </p>
        </div>
        <div class="page-actions">
            <span class="badge-status reportes-count-badge">{{ $reportes->count() }} reportes disponibles</span>
        </div>
    </div>

    <div class="row g-4">
        @foreach($reportes as $item)
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <span class="reportes-card-icon" aria-hidden="true">
                                <i class="bi {{ $iconos[$item['slug']] ?? 'bi-file-earmark-bar-graph' }}"></i>
                            </span>
                            <h2 class="section-title">{{ $item['titulo'] }}</h2>
                        </div>
                        <p class="text-muted small flex-grow-1">{{ $item['descripcion'] }}</p>
                        <a href="{{ route('reportes.show', $item['slug']) }}" class="btn btn-outline-brand mt-2">
                            <i class="bi bi-box-arrow-in-right me-1" aria-hidden="true"></i>Ver reporte
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
