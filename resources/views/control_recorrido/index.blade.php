@extends('layouts.app')

@section('content')
@vite('resources/css/control-recorrido.css')

@php
    $estadoClases = [
        'pendiente' => 'badge-status--pending',
        'en_curso' => 'badge-status--progress',
        'completado' => 'badge-status--active',
        'retrasado' => 'badge-status--warning',
        'cancelado' => 'badge-status--cancelled',
    ];
    $estadoEtiquetas = [
        'pendiente' => 'Pendiente',
        'en_curso' => 'En curso',
        'completado' => 'Finalizado',
        'retrasado' => 'Retrasado',
        'cancelado' => 'Cancelado',
    ];
@endphp

<div class="container-fluid py-4">
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="bi bi-pin-map" aria-hidden="true"></i>
                Control de recorrido
            </h1>
            <p class="page-subtitle">Auditoría automática del cumplimiento de paradas registrado por GPS.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('monitoreo.index') }}" class="btn btn-outline-brand">
                <i class="bi bi-map me-1" aria-hidden="true"></i>Seguimiento en vivo
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('control-recorrido.index') }}" class="row g-3 align-items-end">
                <div class="col-12 col-lg-4">
                    <label for="buscar" class="form-label">Buscar</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="search" id="buscar" name="buscar" value="{{ $buscar }}" class="form-control"
                               placeholder="Conductor, micro, ruta o N.º de asignación">
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <label for="fecha" class="form-label">Fecha</label>
                    <input type="date" id="fecha" name="fecha" value="{{ $fecha }}" class="form-control">
                </div>
                <div class="col-6 col-lg-2">
                    <label for="ruta_id" class="form-label">Ruta</label>
                    <select id="ruta_id" name="ruta_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach($rutas as $r)
                            <option value="{{ $r->id }}" {{ (string) $rutaId === (string) $r->id ? 'selected' : '' }}>{{ $r->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-lg-2">
                    <label for="estado" class="form-label">Estado</label>
                    <select id="estado" name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="en_curso" {{ $estado === 'en_curso' ? 'selected' : '' }}>En curso</option>
                        <option value="pendiente" {{ $estado === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="completado" {{ $estado === 'completado' ? 'selected' : '' }}>Completado</option>
                        <option value="retrasado" {{ $estado === 'retrasado' ? 'selected' : '' }}>Retrasado</option>
                    </select>
                </div>
                <div class="col-6 col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-brand flex-grow-1">
                        <i class="bi bi-funnel me-1" aria-hidden="true"></i>Filtrar
                    </button>
                    @if($buscar !== '' || $rutaId || $estado || $fecha !== now()->toDateString())
                        <a href="{{ route('control-recorrido.index') }}" class="btn btn-outline-brand" title="Limpiar filtros">
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
                        <th>N.º Asignación</th>
                        <th>Conductor / Micro</th>
                        <th>Ruta</th>
                        <th>Turno</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($asignaciones as $a)
                        @php
                            $estadoClase = $estadoClases[$a->estado] ?? 'badge-status--inactive';
                            $estadoEtiqueta = $estadoEtiquetas[$a->estado] ?? ucfirst($a->estado);
                        @endphp
                        <tr>
                            <td>
                                <span class="fw-semibold text-brand d-block">{{ $a->codigo }}</span>
                                <span class="text-muted small">{{ \Carbon\Carbon::parse($a->fecha)->format('d/m/Y') }} · {{ $a->hora_salida ?? 'Sin hora' }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold d-block">{{ $a->conductor ? trim(($a->conductor->nombre ?? '').' '.($a->conductor->apellido ?? '')) : 'Sin conductor' }}</span>
                                <span class="text-muted small">{{ $a->micro->placa ?? 'S/P' }} · Int. {{ $a->micro->interno->numero_interno ?? 'S/I' }}</span>
                            </td>
                            <td>{{ $a->ruta->nombre ?? '—' }}</td>
                            <td class="text-muted">{{ ucfirst($a->turno->nombre ?? '—') }}</td>
                            <td><span class="badge-status {{ $estadoClase }}">{{ $estadoEtiqueta }}</span></td>
                            <td>
                                <div class="table-actions justify-content-end">
                                    <a href="{{ route('control-recorrido.show', $a->id) }}" class="btn btn-outline-brand btn-sm">
                                        <i class="bi bi-eye me-1" aria-hidden="true"></i>Ver
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state py-4">
                                    <i class="bi bi-pin-map" aria-hidden="true"></i>
                                    <div class="empty-state-title">Sin asignaciones</div>
                                    <p class="mb-0">No hay asignaciones para la fecha y filtros seleccionados.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($asignaciones->hasPages())
            <div class="card-footer py-3">
                {{ $asignaciones->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
