@extends('layouts.app')
@section('content')
@vite('resources/css/reportes.css')
<div class="container-fluid py-4">
    @include('reportes.partials.encabezado')

    <div class="card mb-4 reportes-filtros">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="fw-semibold">
                <i class="bi bi-funnel me-1" aria-hidden="true"></i>Filtros del reporte
            </span>
            <span class="badge-status reportes-periodo">Estado actual de la flota</span>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reportes.show', $reporteSlug) }}">
                <div class="row g-3">
                    <div class="col-12 col-md-6 col-lg-3">
                        <label for="filtroPropietario" class="form-label">Propietario</label>
                        <select name="propietario_id" id="filtroPropietario" class="form-select">
                            <option value="">Todos los propietarios</option>
                            @foreach($opciones['propietarios'] as $propietario)
                                <option value="{{ $propietario->id }}" {{ (string) ($filtros['propietario_id'] ?? '') === (string) $propietario->id ? 'selected' : '' }}>
                                    {{ trim($propietario->nombre.' '.$propietario->apellido) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-3 col-lg-2">
                        <label for="filtroInterno" class="form-label">Interno asignado</label>
                        <select name="interno_id" id="filtroInterno" class="form-select">
                            <option value="">Todos</option>
                            @foreach($opciones['internos'] as $interno)
                                <option value="{{ $interno->id }}" {{ (string) ($filtros['interno_id'] ?? '') === (string) $interno->id ? 'selected' : '' }}>
                                    {{ $interno->numero_interno }}
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
                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="filtroBuscar" class="form-label">Buscar</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                            <input type="text" name="buscar" id="filtroBuscar" class="form-control"
                                   placeholder="Placa, marca, modelo, interno o propietario"
                                   value="{{ $filtros['buscar'] ?? '' }}">
                        </div>
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
