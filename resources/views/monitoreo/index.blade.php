@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@vite(['resources/css/monitoreo.css', 'resources/js/monitoreo.js'])

<div class="container-fluid py-4" id="monitoreoApp" data-positions-url="{{ route('monitoreo.posiciones') }}">
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="bi bi-geo-alt" aria-hidden="true"></i>
                Seguimiento GPS en vivo
            </h1>
            <p class="page-subtitle">Posiciones reportadas por las unidades en ruta. El cumplimiento de paradas se consulta en Control de recorrido.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('control-recorrido.index') }}" class="btn btn-outline-brand">
                <i class="bi bi-signpost-split me-1" aria-hidden="true"></i>Control de recorrido
            </a>
        </div>
    </div>

    <div id="monitoreoLiveAlert" class="alert alert-soft-danger d-none" role="alert"></div>

    <div class="row g-3">
        <div class="col-12 col-lg-8 col-xl-9">
            <div class="card h-100">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-map" aria-hidden="true"></i>
                        <span class="fw-semibold">Mapa de posicionamiento GPS</span>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span id="contadorUnidades" class="badge-status badge-status--progress">Cargando unidades</span>
                        <span id="monitoreoUpdatedAt" class="small text-muted">Esperando datos</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    <div id="mapaMonitoreo" class="monitoreo-map"></div>
                    <p class="text-muted small mb-0 px-2 py-2">
                        <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
                        Las unidades sin reporte GPS no se ubican en el mapa. La velocidad solo se muestra cuando existe un reporte.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4 col-xl-3">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-bus-front" aria-hidden="true"></i>
                    <span class="fw-semibold">Unidades en ruta</span>
                </div>
                <div class="card-body p-2">
                    <div class="monitoreo-scroll px-1" id="listaUnidadesContainer">
                        <div class="empty-state py-4">
                            <i class="bi bi-bus-front" aria-hidden="true"></i>
                            <div class="empty-state-title">Cargando unidades</div>
                            <p class="mb-0 small">Consultando las posiciones registradas.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection
