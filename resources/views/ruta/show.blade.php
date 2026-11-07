@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('ruta.index') }}" class="text-decoration-none">Rutas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $ruta->nombre }}</li>
                </ol>
            </nav>

            <div class="page-header">
                <div>
                    <h1 class="page-title"><i class="bi bi-signpost-2" aria-hidden="true"></i>Detalle de la Ruta</h1>
                    <p class="page-subtitle">Información general y recorrido completo (ida y vuelta).</p>
                </div>
                <div class="page-actions">
                    <a href="{{ route('ruta.edit', $ruta->id) }}" class="btn btn-outline-brand">
                        <i class="bi bi-pencil-square me-1" aria-hidden="true"></i>Editar
                    </a>
                    <form action="{{ route('ruta.destroy', $ruta->id) }}" method="POST" class="d-inline" data-confirm="¿Está seguro de eliminar esta ruta?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-trash me-1" aria-hidden="true"></i>Eliminar
                        </button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="panel-muted p-3 mb-3">
                        <div class="form-section-title">Información general</div>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="meta-label">Nombre de la ruta</div>
                                <div class="meta-value">{{ $ruta->nombre }}</div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="meta-label">Total paradas</div>
                                <div class="meta-value d-flex flex-wrap gap-2">
                                    <span class="badge badge-status--progress">Ida: {{ $ruta->paradasIda->count() }}</span>
                                    <span class="badge badge-status--pending">Vuelta: {{ $ruta->paradasVuelta->count() }}</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="meta-label">Estado</div>
                                <div class="meta-value">
                                    @if($ruta->estado === 'activo')
                                        <span class="badge-status badge-status--active">Activo</span>
                                    @else
                                        <span class="badge-status badge-status--inactive">Inactivo</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="meta-label">Descripción</div>
                                <div class="meta-value">{{ $ruta->descripcion ?: 'Sin descripción registrada' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="panel-muted p-3 h-100">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="form-section-title mb-0">
                                        <i class="bi bi-arrow-right-circle me-1" aria-hidden="true"></i>Paradas de ida
                                    </div>
                                    <span class="badge badge-status--progress">{{ $ruta->paradasIda->count() }}</span>
                                </div>
                                @if($ruta->paradasIda->count() > 0)
                                    <div class="list-group list-group-flush">
                                        @foreach($ruta->paradasIda as $parada)
                                            <div class="list-group-item d-flex align-items-center justify-content-between gap-2 px-0 bg-transparent">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="avatar">{{ $parada->pivot->orden }}</span>
                                                    <div>
                                                        <div class="fw-semibold small">{{ $parada->nombre }}</div>
                                                        @if($parada->referencia)
                                                            <small class="text-muted">{{ $parada->referencia }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if($parada->latitud && $parada->longitud)
                                                    <small class="text-muted mono">
                                                        {{ number_format($parada->latitud, 4) }}, {{ number_format($parada->longitud, 4) }}
                                                    </small>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="empty-state py-3">
                                        <i class="bi bi-geo-alt" aria-hidden="true"></i>
                                        <p class="mb-0 small">Sin paradas de ida configuradas.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="panel-muted p-3 h-100">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="form-section-title mb-0">
                                        <i class="bi bi-arrow-left-circle me-1" aria-hidden="true"></i>Paradas de vuelta
                                    </div>
                                    <span class="badge badge-status--pending">{{ $ruta->paradasVuelta->count() }}</span>
                                </div>
                                @if($ruta->paradasVuelta->count() > 0)
                                    <div class="list-group list-group-flush">
                                        @foreach($ruta->paradasVuelta as $parada)
                                            <div class="list-group-item d-flex align-items-center justify-content-between gap-2 px-0 bg-transparent">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="avatar avatar--wine">{{ $parada->pivot->orden }}</span>
                                                    <div>
                                                        <div class="fw-semibold small">{{ $parada->nombre }}</div>
                                                        @if($parada->referencia)
                                                            <small class="text-muted">{{ $parada->referencia }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if($parada->latitud && $parada->longitud)
                                                    <small class="text-muted mono">
                                                        {{ number_format($parada->latitud, 4) }}, {{ number_format($parada->longitud, 4) }}
                                                    </small>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="empty-state py-3">
                                        <i class="bi bi-geo-alt" aria-hidden="true"></i>
                                        <p class="mb-0 small">Sin paradas de vuelta configuradas.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('ruta.index') }}" class="btn btn-outline-brand">
                        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Volver a la lista
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
