@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('interno.index') }}" class="text-decoration-none">Internos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $interno->numero_interno }}</li>
                </ol>
            </nav>

            <div class="page-header">
                <div>
                    <h1 class="page-title"><i class="bi bi-hdd-stack" aria-hidden="true"></i>Detalle del Interno</h1>
                    <p class="page-subtitle">Información registrada del número interno.</p>
                </div>
                <div class="page-actions">
                    <a href="{{ route('interno.edit', $interno->id) }}" class="btn btn-outline-brand">
                        <i class="bi bi-pencil-square me-1" aria-hidden="true"></i>Editar
                    </a>
                    @if($interno->estado !== 'inactivo')
                        <form action="{{ route('interno.destroy', $interno->id) }}" method="POST" class="d-inline" data-confirm="¿Eliminar este interno?">
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
                    <div class="panel-muted p-3 mb-3">
                        <div class="form-section-title">Identificación</div>
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <div class="meta-label">N° Interno</div>
                                <div class="meta-value mono">{{ $interno->numero_interno }}</div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="meta-label">Fecha de ingreso</div>
                                <div class="meta-value">{{ \Carbon\Carbon::parse($interno->fecha_ingreso)->format('d/m/Y H:i') }}</div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="meta-label">Estado</div>
                                <div class="meta-value">
                                    @if($interno->estado === 'disponible')
                                        <span class="badge-status badge-status--active">{{ $interno->estado_badge['label'] }}</span>
                                    @elseif($interno->estado === 'asignado')
                                        <span class="badge-status badge-status--progress">{{ $interno->estado_badge['label'] }}</span>
                                    @else
                                        <span class="badge-status badge-status--inactive">{{ $interno->estado_badge['label'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel-muted p-3">
                        <div class="form-section-title">Observaciones</div>
                        <p class="mb-0">{{ $interno->observaciones ?: 'Sin observaciones.' }}</p>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('interno.index') }}" class="btn btn-outline-brand">
                        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
