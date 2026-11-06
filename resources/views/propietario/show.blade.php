@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('propietario.index') }}" class="text-decoration-none">Propietarios</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $propietario->nombre }} {{ $propietario->apellido }}</li>
                </ol>
            </nav>

            <div class="page-header">
                <div>
                    <h1 class="page-title"><i class="bi bi-person-vcard" aria-hidden="true"></i>Detalle del Propietario</h1>
                    <p class="page-subtitle">Información registrada del propietario.</p>
                </div>
                <div class="page-actions">
                    <a href="{{ route('propietario.edit', $propietario->id) }}" class="btn btn-outline-brand">
                        <i class="bi bi-pencil-square me-1" aria-hidden="true"></i>Editar
                    </a>
                    @if(($propietario->estado ?? 'activo') === 'activo')
                        <form action="{{ route('propietario.destroy', $propietario->id) }}" method="POST" class="d-inline" data-confirm="¿Eliminar este propietario?">
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
                        <span class="avatar">{{ strtoupper(substr($propietario->nombre, 0, 1)) }}{{ strtoupper(substr($propietario->apellido, 0, 1)) }}</span>
                        <div>
                            <div class="section-title">{{ $propietario->nombre }} {{ $propietario->apellido }}</div>
                            <div class="text-muted small">{{ $propietario->correo }}</div>
                        </div>
                    </div>

                    <div class="panel-muted p-3 mb-3">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="meta-label">Nombre completo</div>
                                <div class="meta-value">{{ $propietario->nombre }} {{ $propietario->apellido }}</div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="meta-label">CI</div>
                                <div class="meta-value mono">{{ $propietario->ci }}</div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="meta-label">Estado</div>
                                <div class="meta-value">
                                    @if(($propietario->estado ?? 'activo') === 'activo')
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
                                <div class="meta-value">{{ $propietario->correo }}</div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="meta-label">Teléfono</div>
                                <div class="meta-value">{{ $propietario->telefono ?? '—' }}</div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="meta-label">Micros asignados</div>
                                <div class="meta-value">{{ $propietario->micros ? $propietario->micros->count() : 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('propietario.index') }}" class="btn btn-outline-brand">
                        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
