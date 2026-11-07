@extends('layouts.app')
@section('content')
@vite('resources/css/reportes.css')
<div class="container-fluid py-4">
    @include('reportes.partials.encabezado')

    @php
        $desde = $filtros['desde'] ?? null;
        $hasta = $filtros['hasta'] ?? null;
        if ($desde !== null && $hasta !== null) {
            $periodo = 'Período: '.\Carbon\Carbon::parse($desde)->format('d/m/Y').' al '.\Carbon\Carbon::parse($hasta)->format('d/m/Y');
        } elseif ($desde !== null) {
            $periodo = 'Desde: '.\Carbon\Carbon::parse($desde)->format('d/m/Y');
        } elseif ($hasta !== null) {
            $periodo = 'Hasta: '.\Carbon\Carbon::parse($hasta)->format('d/m/Y');
        } else {
            $periodo = 'Sin límite de fechas';
        }
    @endphp

    <div class="card mb-4 reportes-filtros">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="fw-semibold">
                <i class="bi bi-funnel me-1" aria-hidden="true"></i>Filtros del reporte
            </span>
            <span class="badge-status reportes-periodo">{{ $periodo }}</span>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reportes.show', $reporteSlug) }}">
                <div class="row g-3">
                    <div class="col-12 col-md-6 col-lg-3">
                        <label for="filtroRuta" class="form-label">Ruta</label>
                        <select name="ruta_id" id="filtroRuta" class="form-select">
                            <option value="">Todas las rutas</option>
                            @foreach($opciones['rutas'] as $ruta)
                                <option value="{{ $ruta->id }}" {{ (string) ($filtros['ruta_id'] ?? '') === (string) $ruta->id ? 'selected' : '' }}>
                                    {{ $ruta->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="filtroConductor" class="form-label">Conductor</label>
                        <select name="conductor_id" id="filtroConductor" class="form-select">
                            <option value="">Todos los conductores</option>
                            @foreach($opciones['conductores'] as $conductor)
                                <option value="{{ $conductor->id }}" {{ (string) ($filtros['conductor_id'] ?? '') === (string) $conductor->id ? 'selected' : '' }}>
                                    {{ $conductor->nombre_completo }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-3 col-lg-2">
                        <label for="filtroDesde" class="form-label">Desde</label>
                        <input type="date" name="desde" id="filtroDesde" value="{{ $filtros['desde'] ?? '' }}" class="form-control">
                    </div>
                    <div class="col-6 col-md-3 col-lg-2">
                        <label for="filtroHasta" class="form-label">Hasta</label>
                        <input type="date" name="hasta" id="filtroHasta" value="{{ $filtros['hasta'] ?? '' }}" class="form-control">
                    </div>
                </div>
                <div class="d-flex flex-wrap justify-content-end gap-2 mt-3">
                    <a href="{{ route('reportes.show', $reporteSlug) }}" class="btn btn-outline-brand">
                        <i class="bi bi-arrow-counterclockwise me-1" aria-hidden="true"></i>Limpiar
                    </a>
                    <button type="submit" class="btn btn-brand">
                        <i class="bi bi-search me-1" aria-hidden="true"></i>Consultar
                    </button>
                </div>
            </form>
        </div>
    </div>

    @include('reportes.partials.resumen')
    @include('reportes.partials.tabla')
</div>
@endsection
