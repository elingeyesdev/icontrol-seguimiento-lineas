@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-7">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('turno.index') }}" class="text-decoration-none">Turnos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $turno->nombre_label }}</li>
                </ol>
            </nav>

            <div class="page-header">
                <div>
                    <h1 class="page-title"><i class="bi bi-clock" aria-hidden="true"></i>Detalle del Turno</h1>
                    <p class="page-subtitle">Información del catálogo de horarios.</p>
                </div>
                <div class="page-actions">
                    <a href="{{ route('turno.edit', $turno->id) }}" class="btn btn-outline-brand">
                        <i class="bi bi-pencil-square me-1" aria-hidden="true"></i>Editar
                    </a>
                    @if($turno->estado === 'activo')
                        <form action="{{ route('turno.destroy', $turno->id) }}" method="POST" class="d-inline" data-confirm="¿Eliminar el turno {{ $turno->nombre_label }}?">
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
                    @php
                        $iconos = ['mañana' => 'bi-sun', 'tarde' => 'bi-cloud-sun', 'noche' => 'bi-moon-stars'];
                    @endphp

                    <div class="d-flex align-items-center gap-3 mb-4">
                        <span class="avatar avatar--wine"><i class="bi {{ $iconos[$turno->nombre] ?? 'bi-clock' }}" aria-hidden="true"></i></span>
                        <div>
                            <div class="section-title">{{ $turno->nombre_label }}</div>
                            <div class="text-muted small">
                                @if($turno->estado === 'activo')
                                    <span class="badge-status badge-status--active">Activo</span>
                                @else
                                    <span class="badge-status badge-status--inactive">Inactivo</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="panel-muted p-3 mb-3">
                        <div class="form-section-title">Horario</div>
                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <div class="meta-label">Hora de inicio</div>
                                <div class="meta-value mono">{{ \Carbon\Carbon::parse($turno->hora_inicio)->format('H:i') }}</div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="meta-label">Hora de fin</div>
                                <div class="meta-value mono">{{ \Carbon\Carbon::parse($turno->hora_fin)->format('H:i') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="panel-muted p-3">
                        <div class="form-section-title">Detalles</div>
                        <div class="row g-3">
                            @if($turno->descripcion)
                                <div class="col-12">
                                    <div class="meta-label">Descripción</div>
                                    <div class="meta-value">{{ $turno->descripcion }}</div>
                                </div>
                            @endif
                            <div class="col-12">
                                <div class="meta-label">Asignaciones registradas</div>
                                <div class="meta-value">{{ $turno->asignaciones()->count() }} asignación(es)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <a href="{{ route('turno.index') }}" class="btn btn-outline-brand">
                        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Volver al catálogo
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
