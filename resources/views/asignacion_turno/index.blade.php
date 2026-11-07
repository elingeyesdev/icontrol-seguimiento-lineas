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
@endphp

<div class="container-fluid py-4">
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="bi bi-calendar2-check" aria-hidden="true"></i>
                Asignación de turnos
            </h1>
            <p class="page-subtitle">Programación de turnos para conductores, micros y rutas.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('asignacion-turno.create') }}" class="btn btn-brand">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Nueva asignación
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-soft-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2" aria-hidden="true"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-soft-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle me-2" aria-hidden="true"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="meta-label">Turnos cubiertos hoy</div>
                    <div class="h3 fw-semibold mb-0">{{ $stats['turnos_cubiertos'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="meta-label">Conductores libres</div>
                    <div class="h3 fw-semibold mb-0">{{ $stats['conductores_libres'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="meta-label">Unidades en ruta</div>
                    <div class="h3 fw-semibold mb-0">{{ $stats['unidades_en_ruta'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('asignacion-turno.index') }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-3">
                    <label for="fecha" class="form-label">Fecha</label>
                    <input type="date" id="fecha" name="fecha" value="{{ $fecha }}" class="form-control">
                </div>
                <div class="col-12 col-md-7">
                    <label for="buscar" class="form-label">Buscar</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="text" id="buscar" name="buscar" class="form-control"
                               placeholder="Conductor, código o placa" value="{{ $buscar ?? '' }}">
                    </div>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-brand flex-grow-1">
                        <i class="bi bi-funnel me-1" aria-hidden="true"></i>Filtrar
                    </button>
                    @if($buscar || $fecha !== now()->toDateString())
                        <a href="{{ route('asignacion-turno.index') }}" class="btn btn-outline-brand" title="Limpiar filtros">
                            <i class="bi bi-x-lg" aria-hidden="true"></i>
                            <span class="visually-hidden">Limpiar filtros</span>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-list-check" aria-hidden="true"></i>
                <span class="fw-semibold">Asignaciones de turno ({{ $asignaciones->total() }} registros)</span>
            </div>
            <span class="badge-status badge-status--inactive">{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Fecha</th>
                        <th>Turno</th>
                        <th>Conductor</th>
                        <th>Unidad / micro</th>
                        <th>Ruta</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($asignaciones as $a)
                        @php
                            $estadoClase = $estadoClases[$a->estado] ?? 'badge-status--inactive';
                            $codigoAsignacion = 'AT-' . str_pad($a->id, 3, '0', STR_PAD_LEFT);
                        @endphp
                        <tr>
                            <td class="fw-semibold text-brand">{{ $codigoAsignacion }}</td>
                            <td>{{ \Carbon\Carbon::parse($a->fecha)->format('d/m/Y') }}</td>
                            <td>{{ $a->turno?->nombre_label ?? '—' }}</td>
                            <td>
                                @if($a->conductor)
                                    <span class="d-block fw-semibold">{{ $a->conductor->nombre }} {{ $a->conductor->apellido }}</span>
                                    <span class="text-muted small">Lic. {{ $a->conductor->licencia ?? 'S/L' }}</span>
                                @else
                                    <span class="text-muted">Sin conductor</span>
                                @endif
                            </td>
                            <td>
                                <span class="d-block fw-semibold">{{ $a->micro->placa ?? 'S/P' }}</span>
                                <span class="text-muted small">Int. {{ $a->micro->interno->numero_interno ?? 'S/I' }} · {{ $a->micro->modelo ?? 'Sin modelo' }}</span>
                            </td>
                            <td class="text-muted">{{ $a->ruta?->nombre ?? '—' }}</td>
                            <td><span class="badge-status {{ $estadoClase }}">{{ $a->estado_badge['label'] }}</span></td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('asignacion-turno.show', $a->id) }}" class="btn btn-outline-brand btn-sm-action" title="Ver detalle">
                                        <i class="bi bi-eye me-1" aria-hidden="true"></i>Ver
                                    </a>
                                    <a href="{{ route('asignacion-turno.edit', $a->id) }}" class="btn btn-outline-brand btn-sm-action" title="Editar asignación">
                                        <i class="bi bi-pencil-square me-1" aria-hidden="true"></i>Editar
                                    </a>
                                    @if($a->estado !== 'cancelado')
                                        <form action="{{ route('asignacion-turno.destroy', $a->id) }}" method="POST" class="d-inline"
                                              data-confirm="¿Cancelar la asignación {{ $codigoAsignacion }}? Esta acción cambia su estado a cancelada.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-brand btn-sm-action" title="Cancelar asignación">
                                                <i class="bi bi-x-circle me-1" aria-hidden="true"></i>Cancelar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state py-4">
                                    <i class="bi bi-calendar-x" aria-hidden="true"></i>
                                    <div class="empty-state-title">Sin asignaciones</div>
                                    <p class="mb-0">No se encontraron asignaciones para la fecha o búsqueda seleccionada.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($asignaciones->hasPages())
            <div class="card-footer py-3">
                {{ $asignaciones->appends(['buscar' => $buscar, 'fecha' => $fecha])->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
