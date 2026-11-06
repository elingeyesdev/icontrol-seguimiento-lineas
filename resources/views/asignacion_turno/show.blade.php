@extends('layouts.app')

@section('content')
@php
    $estadoClases = [
        'pendiente' => 'badge-status--pending',
        'en_curso' => 'badge-status--progress',
        'completado' => 'badge-status--active',
        'retrasado' => 'badge-status--warning',
        'cancelado' => 'badge-status--cancelled',
    ];
    $estadoClase = $estadoClases[$asignacion->estado] ?? 'badge-status--inactive';
    $codigoAsignacion = 'AT-' . str_pad($asignacion->id, 3, '0', STR_PAD_LEFT);
@endphp

<div class="container-fluid py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('asignacion-turno.index') }}" class="text-decoration-none">Asignación de turnos</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $codigoAsignacion }}</li>
        </ol>
    </nav>

    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="bi bi-calendar2-check" aria-hidden="true"></i>
                Detalle de asignación
            </h1>
            <p class="page-subtitle">{{ \Carbon\Carbon::parse($asignacion->fecha)->translatedFormat('l, d \d\e F \d\e Y') }}</p>
        </div>
        <div class="page-actions">
            <span class="badge-status {{ $estadoClase }}">{{ ucfirst(str_replace('_', ' ', $asignacion->estado)) }}</span>
            <a href="{{ route('asignacion-turno.edit', $asignacion->id) }}" class="btn btn-outline-brand">
                <i class="bi bi-pencil-square me-1" aria-hidden="true"></i>Editar
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-9">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <span class="fw-semibold">Código {{ $codigoAsignacion }}</span>
                    <span class="small text-muted">{{ $asignacion->turno?->nombre_label ?? 'Sin turno' }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="panel-muted p-3 h-100">
                                <div class="meta-label"><i class="bi bi-clock me-1" aria-hidden="true"></i>Turno</div>
                                <div class="meta-value">{{ $asignacion->turno?->nombre_label ?? '—' }}</div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="panel-muted p-3 h-100">
                                <div class="meta-label"><i class="bi bi-signpost-2 me-1" aria-hidden="true"></i>Ruta</div>
                                <div class="meta-value">{{ $asignacion->ruta?->nombre ?? '—' }}</div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="panel-muted p-3 h-100">
                                <div class="meta-label"><i class="bi bi-person-badge me-1" aria-hidden="true"></i>Conductor</div>
                                @if($asignacion->conductor)
                                    <div class="meta-value">{{ $asignacion->conductor->nombre }} {{ $asignacion->conductor->apellido }}</div>
                                    <div class="text-muted small">CI: {{ $asignacion->conductor->ci }}</div>
                                @else
                                    <div class="meta-value">Sin conductor</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="panel-muted p-3 h-100">
                                <div class="meta-label"><i class="bi bi-bus-front me-1" aria-hidden="true"></i>Micro / interno</div>
                                @if($asignacion->micro)
                                    <div class="meta-value">{{ $asignacion->micro->placa }}</div>
                                    <div class="text-muted small">{{ $asignacion->micro->marca }} {{ $asignacion->micro->modelo }}</div>
                                @else
                                    <div class="meta-value">Sin micro</div>
                                @endif
                                @if($asignacion->interno)
                                    <div class="text-muted small mt-1">
                                        <i class="bi bi-hdd-stack me-1" aria-hidden="true"></i>Interno {{ $asignacion->interno->numero_interno }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="panel-muted p-3 h-100">
                                <div class="meta-label"><i class="bi bi-clock-history me-1" aria-hidden="true"></i>Horario operativo</div>
                                <div class="d-flex gap-4">
                                    <div>
                                        <div class="meta-label">Salida</div>
                                        <div class="meta-value mono">{{ $asignacion->hora_salida ? \Carbon\Carbon::parse($asignacion->hora_salida)->format('H:i') : 'Sin registro' }}</div>
                                    </div>
                                    <div>
                                        <div class="meta-label">Llegada</div>
                                        <div class="meta-value mono">{{ $asignacion->hora_llegada ? \Carbon\Carbon::parse($asignacion->hora_llegada)->format('H:i') : 'Sin registro' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($asignacion->observaciones)
                            <div class="col-12 col-md-6">
                                <div class="panel-muted p-3 h-100">
                                    <div class="meta-label"><i class="bi bi-chat-text me-1" aria-hidden="true"></i>Observaciones</div>
                                    <div class="small">{{ $asignacion->observaciones }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card-footer d-flex flex-wrap gap-2 justify-content-end">
                    <a href="{{ route('asignacion-turno.index') }}" class="btn btn-outline-brand">
                        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Volver
                    </a>
                    @if($asignacion->estado !== 'cancelado')
                        <form action="{{ route('asignacion-turno.destroy', $asignacion->id) }}" method="POST"
                              data-confirm="¿Cancelar la asignación {{ $codigoAsignacion }}? Esta acción cambia su estado a cancelada.">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-wine">
                                <i class="bi bi-x-circle me-1" aria-hidden="true"></i>Cancelar asignación
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
