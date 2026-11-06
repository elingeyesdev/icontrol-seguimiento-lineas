@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('rutas-paradas.index') }}" class="text-decoration-none">Rutas y Paradas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detalle de la asociación</li>
                </ol>
            </nav>

            <div class="page-header">
                <div>
                    <h1 class="page-title"><i class="bi bi-diagram-3" aria-hidden="true"></i>Detalle de la Asociación</h1>
                    <p class="page-subtitle">Información de la relación ruta-parada.</p>
                </div>
                <div class="page-actions">
                    <a href="{{ route('rutas-paradas.edit', $rutaParada->id) }}" class="btn btn-brand">
                        <i class="bi bi-pencil-square me-1" aria-hidden="true"></i>Editar
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="panel-muted p-3">
                        <div class="form-section-title">Ruta y parada</div>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="meta-label">Ruta</div>
                                <div class="meta-value">
                                    @if($rutaParada->ruta)
                                        {{ $rutaParada->ruta->nombre }}
                                        @if($rutaParada->ruta->descripcion)
                                            <span class="text-muted d-block small">{{ $rutaParada->ruta->descripcion }}</span>
                                        @endif
                                    @else
                                        <span class="text-danger">Ruta no asignada o eliminada</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="meta-label">Parada</div>
                                <div class="meta-value">
                                    @if($rutaParada->parada)
                                        {{ $rutaParada->parada->nombre }}
                                        <span class="text-muted d-block small">{{ $rutaParada->parada->referencia }}</span>
                                    @else
                                        <span class="text-danger">Parada no asignada o eliminada</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <div class="meta-label">Orden en el recorrido</div>
                                <div class="meta-value">{{ $rutaParada->orden }}</div>
                            </div>

                            <div class="col-12 col-md-4">
                                <div class="meta-label">Sentido</div>
                                <div class="meta-value"><span class="badge badge-status--inactive">{{ ucfirst($rutaParada->sentido) }}</span></div>
                            </div>

                            <div class="col-12 col-md-4">
                                <div class="meta-label">Estado</div>
                                <div class="meta-value">
                                    <span class="badge-status {{ $rutaParada->estado === 'activo' ? 'badge-status--active' : 'badge-status--inactive' }}">
                                        {{ ucfirst($rutaParada->estado) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('rutas-paradas.index') }}" class="btn btn-outline-brand">
                        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Volver al Listado
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
