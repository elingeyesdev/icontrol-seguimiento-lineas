@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('micro.index') }}" class="text-decoration-none">Micros</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $micro->placa }}</li>
                </ol>
            </nav>

            <div class="page-header">
                <div>
                    <h1 class="page-title"><i class="bi bi-bus-front" aria-hidden="true"></i>Detalle del Micro</h1>
                    <p class="page-subtitle">Información registrada del vehículo.</p>
                </div>
                <div class="page-actions">
                    <a href="{{ route('micro.edit', $micro->id) }}" class="btn btn-outline-brand">
                        <i class="bi bi-pencil-square me-1" aria-hidden="true"></i>Editar
                    </a>
                    @if($micro->estado === 'activo')
                        <form action="{{ route('micro.destroy', $micro->id) }}" method="POST" class="d-inline" data-confirm="¿Eliminar este micro?">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-trash me-1" aria-hidden="true"></i>Eliminar
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <span class="avatar"><i class="bi bi-bus-front" aria-hidden="true"></i></span>
                        <div>
                            <div class="section-title mono">{{ $micro->placa }}</div>
                            <div class="text-muted small">{{ $micro->marca }} {{ $micro->modelo }}{{ $micro->anio_fabricacion ? ' · ' . $micro->anio_fabricacion : '' }}</div>
                        </div>
                    </div>

                    <div class="panel-muted p-3 mb-3">
                        <div class="form-section-title">Vehículo</div>
                        <div class="row g-3">
                            <div class="col-12 col-md-3">
                                <div class="meta-label">Placa</div>
                                <div class="meta-value mono">{{ $micro->placa }}</div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="meta-label">Marca</div>
                                <div class="meta-value">{{ $micro->marca }}</div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="meta-label">Modelo</div>
                                <div class="meta-value">{{ $micro->modelo }}</div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="meta-label">Estado</div>
                                <div class="meta-value">
                                    @if($micro->estado === 'activo')
                                        <span class="badge-status badge-status--active">Activo</span>
                                    @else
                                        <span class="badge-status badge-status--inactive">Inactivo</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="meta-label">Chasis</div>
                                <div class="meta-value mono">{{ $micro->chasis ?? '—' }}</div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="meta-label">Año de fabricación</div>
                                <div class="meta-value">{{ $micro->anio_fabricacion ?? '—' }}</div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="meta-label">Capacidad</div>
                                <div class="meta-value">{{ $micro->capacidad_pasajeros }} pasajeros</div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="meta-label">N° interno</div>
                                <div class="meta-value mono">{{ $micro->interno->numero_interno ?? '—' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="panel-muted p-3">
                        <div class="form-section-title">Asignación</div>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="meta-label">Propietario</div>
                                <div class="meta-value">{{ $micro->propietario ? $micro->propietario->nombre . ' ' . $micro->propietario->apellido : '—' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('micro.index') }}" class="btn btn-outline-brand">
                        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
