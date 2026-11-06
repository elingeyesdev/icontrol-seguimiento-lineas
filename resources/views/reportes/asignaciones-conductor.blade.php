@extends('layouts.app')
@section('content')
@vite('resources/css/reportes.css')
<div class="container-fluid py-4">
    @include('reportes.partials.encabezado')

    @php
        $anioSeleccionado = (int) ($filtros['anio'] ?? now()->year);
        $mesSeleccionado = (int) ($filtros['mes'] ?? now()->month);
        $periodo = ucfirst(\Carbon\Carbon::createFromDate($anioSeleccionado, $mesSeleccionado, 1)->translatedFormat('F Y'));
    @endphp

    <div class="card mb-4 reportes-filtros">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="fw-semibold">
                <i class="bi bi-funnel me-1" aria-hidden="true"></i>Filtros del reporte
            </span>
            <span class="badge-status reportes-periodo">Período: {{ $periodo }}</span>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reportes.show', $reporteSlug) }}">
                <div class="row g-3">
                    <div class="col-6 col-md-3 col-lg-2">
                        <label for="filtroMes" class="form-label">Mes</label>
                        <select name="mes" id="filtroMes" class="form-select">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $mesSeleccionado === $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-6 col-md-3 col-lg-2">
                        <label for="filtroAnio" class="form-label">Año</label>
                        <select name="anio" id="filtroAnio" class="form-select">
                            @foreach(range(now()->year - 3, now()->year + 1) as $anio)
                                <option value="{{ $anio }}" {{ $anioSeleccionado === $anio ? 'selected' : '' }}>{{ $anio }}</option>
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
                        <label for="filtroEstado" class="form-label">Estado</label>
                        <select name="estado" id="filtroEstado" class="form-select">
                            <option value="">Todos</option>
                            @foreach($opciones['estados'] as $valor => $etiqueta)
                                <option value="{{ $valor }}" {{ ($filtros['estado'] ?? '') === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
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
