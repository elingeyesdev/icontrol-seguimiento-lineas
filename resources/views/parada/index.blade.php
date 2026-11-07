@extends('layouts.app')

@section('content')
@vite('resources/css/catalogos.css')
<div class="container-fluid py-4">
    <div class="page-header">
        <div>
            <h1 class="page-title"><i class="bi bi-geo-alt" aria-hidden="true"></i>Gestión de Paradas</h1>
            <p class="page-subtitle">Administra las paradas del sistema de transporte.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('parada.create') }}" class="btn btn-brand">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Registrar Parada
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-soft-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route('parada.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-6 col-lg-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="text" name="buscar" class="form-control" placeholder="Buscar paradas" value="{{ $buscar ?? '' }}" aria-label="Buscar paradas">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-outline-brand">Buscar</button>
                </div>
                @if(!empty($buscar))
                    <div class="col-auto">
                        <a href="{{ route('parada.index') }}" class="btn btn-outline-brand btn-icon" aria-label="Limpiar búsqueda">
                            <i class="bi bi-x-lg" aria-hidden="true"></i>
                        </a>
                    </div>
                @endif
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nombre</th>
                            <th>Referencia</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paradas as $parada)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration + ($paradas->currentPage() - 1) * $paradas->perPage() }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="avatar"><i class="bi bi-geo-alt" aria-hidden="true"></i></span>
                                        <span class="fw-semibold">{{ $parada->nombre }}</span>
                                    </div>
                                </td>
                                <td class="text-muted">{{ $parada->referencia ?? '—' }}</td>
                                <td>
                                    @if($parada->estado === 'activo')
                                        <span class="badge-status badge-status--active">Activo</span>
                                    @else
                                        <span class="badge-status badge-status--inactive">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="table-actions">
                                        @if($parada->latitud && $parada->longitud)
                                            <button type="button" class="btn btn-outline-brand btn-sm-action"
                                                    data-bs-toggle="modal" data-bs-target="#mapModal"
                                                    data-lat="{{ $parada->latitud }}" data-lng="{{ $parada->longitud }}"
                                                    data-nombre="{{ $parada->nombre }}" title="Ver en mapa">
                                                <i class="bi bi-map me-1" aria-hidden="true"></i>Mapa
                                            </button>
                                        @endif
                                        <a href="{{ route('parada.show', $parada->id) }}" class="btn btn-outline-brand btn-sm-action">
                                            <i class="bi bi-eye me-1" aria-hidden="true"></i>Ver
                                        </a>
                                        <a href="{{ route('parada.edit', $parada->id) }}" class="btn btn-outline-brand btn-sm-action">
                                            <i class="bi bi-pencil-square me-1" aria-hidden="true"></i>Editar
                                        </a>
                                        @if($parada->estado === 'activo')
                                            <form action="{{ route('parada.destroy', $parada->id) }}" method="POST" class="d-inline" data-confirm="¿Eliminar esta parada?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm-action">
                                                    <i class="bi bi-trash me-1" aria-hidden="true"></i>Eliminar
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="bi bi-geo-alt" aria-hidden="true"></i>
                                        <div class="empty-state-title">Sin paradas registradas</div>
                                        <p class="mb-2">No se encontraron paradas con los criterios actuales.</p>
                                        <a href="{{ route('parada.create') }}" class="btn btn-outline-brand btn-sm-action">
                                            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Crear primera parada
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($paradas->hasPages())
            <div class="card-footer">
                {{ $paradas->appends(['buscar' => $buscar])->links() }}
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title section-title" id="mapModalLabel">
                    <i class="bi bi-map me-1" aria-hidden="true"></i>Ubicación de la Parada
                </h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-0">
                <div id="modalMap" class="parada-map parada-map--modal"></div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

@vite('resources/js/parada-map.js')
@endsection
