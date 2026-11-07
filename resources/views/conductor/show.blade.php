@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('conductor.index') }}" class="text-decoration-none">Conductores</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $conductor->nombre }} {{ $conductor->apellido }}</li>
                </ol>
            </nav>

            <div class="page-header">
                <div>
                    <h1 class="page-title"><i class="bi bi-person-badge" aria-hidden="true"></i>Detalle del Conductor</h1>
                    <p class="page-subtitle">Información registrada del conductor.</p>
                </div>
                <div class="page-actions">
                    <a href="{{ route('conductor.edit', $conductor->id) }}" class="btn btn-outline-brand">
                        <i class="bi bi-pencil-square me-1" aria-hidden="true"></i>Editar
                    </a>
                    @if(($conductor->estado ?? 'activo') === 'activo')
                        <form action="{{ route('conductor.destroy', $conductor->id) }}" method="POST" class="d-inline" data-confirm="¿Eliminar este conductor?">
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
                        <span class="avatar avatar--wine">{{ strtoupper(substr($conductor->nombre, 0, 1)) }}{{ strtoupper(substr($conductor->apellido, 0, 1)) }}</span>
                        <div>
                            <div class="section-title">{{ $conductor->nombre }} {{ $conductor->apellido }}</div>
                            <div class="text-muted small">{{ $conductor->correo }}</div>
                        </div>
                    </div>

                    <div class="panel-muted p-3 mb-3">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="meta-label">Nombre completo</div>
                                <div class="meta-value">{{ $conductor->nombre }} {{ $conductor->apellido }}</div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="meta-label">CI</div>
                                <div class="meta-value mono">{{ $conductor->ci }}</div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="meta-label">Estado</div>
                                <div class="meta-value">
                                    @if(($conductor->estado ?? 'activo') === 'activo')
                                        <span class="badge-status badge-status--active">Activo</span>
                                    @else
                                        <span class="badge-status badge-status--inactive">Inactivo</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel-muted p-3">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="meta-label">Correo electrónico</div>
                                <div class="meta-value">{{ $conductor->correo }}</div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="meta-label">Teléfono</div>
                                <div class="meta-value">{{ $conductor->telefono }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('conductor.index') }}" class="btn btn-outline-brand">
                        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
