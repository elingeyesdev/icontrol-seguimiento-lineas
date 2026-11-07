@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@vite(['resources/css/dashboard.css', 'resources/js/dashboard.js'])

<div class="container-fluid py-4"
     id="dashboardApp"
     data-positions-url="{{ route('monitoreo.posiciones') }}"
     data-route-base="{{ url('control-recorrido') }}">

    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="bi bi-grid-1x2" aria-hidden="true"></i>
                Panel de control operativo
            </h1>
            <p class="page-subtitle">Monitoreo de flota y auditoría de recorridos con datos del sistema.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('monitoreo.index') }}" class="btn btn-outline-brand">
                <i class="bi bi-geo-alt me-1" aria-hidden="true"></i>Seguimiento GPS
            </a>
            <a href="{{ route('control-recorrido.index') }}" class="btn btn-wine">
                <i class="bi bi-signpost-split me-1" aria-hidden="true"></i>Control de recorrido
            </a>
        </div>
    </div>

    <div id="dashboardLiveAlert" class="alert alert-soft-danger d-none" role="alert"></div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <div class="meta-label">Micros activos</div>
                            <div class="dashboard-metric">{{ $microsActivos }}</div>
                        </div>
                        <span class="dashboard-metric-icon" aria-hidden="true"><i class="bi bi-bus-front"></i></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center gap-2 mt-3 pt-3 border-top">
                        <span class="badge-status badge-status--active">Registrados</span>
                        <span class="small text-muted">Estado activo</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <div class="meta-label">Conductores activos</div>
                            <div class="dashboard-metric">{{ $conductoresDisponibles }}</div>
                        </div>
                        <span class="dashboard-metric-icon" aria-hidden="true"><i class="bi bi-person-badge"></i></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center gap-2 mt-3 pt-3 border-top">
                        <span class="badge-status badge-status--active">Registrados</span>
                        <span class="small text-muted">Estado activo</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <div class="meta-label">Recorridos de hoy</div>
                            <div class="dashboard-metric">{{ $recorridosActivos }}</div>
                        </div>
                        <span class="dashboard-metric-icon" aria-hidden="true"><i class="bi bi-pin-map"></i></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center gap-2 mt-3 pt-3 border-top">
                        <span class="badge-status badge-status--progress">Asignaciones</span>
                        <span class="small text-muted">Pendientes o en curso</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <div class="meta-label">Micros fuera de servicio</div>
                            <div class="dashboard-metric">{{ $microsFueraServicio }}</div>
                        </div>
                        <span class="dashboard-metric-icon" aria-hidden="true"><i class="bi bi-tools"></i></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center gap-2 mt-3 pt-3 border-top">
                        <span class="badge-status badge-status--warning">No activos</span>
                        <span class="small text-muted">Según registro</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-start gap-3">
                <span class="dashboard-metric-icon" aria-hidden="true"><i class="bi bi-signpost-split"></i></span>
                <div>
                    <h2 class="section-title">Control de recorrido</h2>
                    <p class="text-muted small mb-0">Auditoría automática del cumplimiento de paradas registrado por GPS.</p>
                </div>
            </div>
            <a href="{{ route('control-recorrido.index') }}" class="btn btn-outline-brand">
                <i class="bi bi-arrow-right me-1" aria-hidden="true"></i>Abrir control de recorrido
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-8">
            <div class="card h-100">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-geo-alt" aria-hidden="true"></i>
                        <span class="fw-semibold">Mapa de posicionamiento GPS</span>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span id="contadorUnidades" class="badge-status badge-status--progress">Cargando unidades</span>
                        <span id="dashboardUpdatedAt" class="small text-muted">Esperando datos</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    <div id="mapaMonitoreo" class="dashboard-map"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4 d-flex flex-column gap-4">
            <div class="card flex-grow-1">
                <div class="card-header d-flex justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-bus-front" aria-hidden="true"></i>
                        <span class="fw-semibold">Unidades en ruta</span>
                    </div>
                    <span class="small text-muted">Seleccionar</span>
                </div>
                <div class="card-body dashboard-scroll" id="listaUnidadesContainer">
                    <div class="empty-state py-4">
                        <i class="bi bi-bus-front" aria-hidden="true"></i>
                        <div class="empty-state-title">Cargando unidades</div>
                        <p class="mb-0 small">Consultando las posiciones registradas.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h2 class="section-title">Estado de la flota y recorridos</h2>
                <p class="text-muted small mb-0">Resumen de las asignaciones activas del día.</p>
            </div>
            <a href="{{ route('control-recorrido.index') }}" class="btn btn-outline-brand btn-sm-action">
                Ver todos los recorridos
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Unidad / placa</th>
                        <th>Conductor</th>
                        <th>Ruta</th>
                        <th>Estado</th>
                        <th>Velocidad</th>
                        <th>Última actualización</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaFlotaBody">
                    <tr>
                        <td colspan="7">
                            <div class="empty-state py-4">
                                <i class="bi bi-bus-front" aria-hidden="true"></i>
                                <div class="empty-state-title">Cargando flota</div>
                                <p class="mb-0 small">Consultando las asignaciones activas.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection
