@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="page-header">
        <div>
            <h1 class="page-title"><i class="bi bi-bus-front" aria-hidden="true"></i>Gestión de Micros</h1>
            <p class="page-subtitle">Administra los vehículos del sistema.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('micro.create') }}" class="btn btn-brand">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Registrar Micro
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-soft-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route('micro.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-6 col-lg-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="text" name="buscar" class="form-control" placeholder="Buscar por placa, modelo o marca" value="{{ $buscar ?? '' }}" aria-label="Buscar micros">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-outline-brand">Buscar</button>
                </div>
                @if(!empty($buscar))
                    <div class="col-auto">
                        <a href="{{ route('micro.index') }}" class="btn btn-outline-brand btn-icon" aria-label="Limpiar búsqueda">
                            <i class="bi bi-x-lg" aria-hidden="true"></i>
                        </a>
                    </div>
                @endif
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Placa</th>
                            <th>Vehículo</th>
                            <th>Propietario</th>
                            <th>Interno</th>
                            <th>Capacidad</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($micros as $micro)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration + ($micros->currentPage() - 1) * $micros->perPage() }}</td>
                                <td><span class="badge bg-brand-subtle text-brand mono">{{ $micro->placa }}</span></td>
                                <td>
                                    <div class="fw-semibold">{{ $micro->marca }} {{ $micro->modelo }}</div>
                                    <small class="text-muted">{{ $micro->anio_fabricacion ?? 'Sin año' }}</small>
                                </td>
                                <td class="text-muted">
                                    @if($micro->propietario)
                                        {{ $micro->propietario->nombre }} {{ $micro->propietario->apellido }}
                                    @else
                                        <span class="text-danger">—</span>
                                    @endif
                                </td>
                                <td class="text-muted mono">{{ $micro->interno->numero_interno ?? '—' }}</td>
                                <td class="text-muted">{{ $micro->capacidad_pasajeros }} pax</td>
                                <td>
                                    @if($micro->estado === 'activo')
                                        <span class="badge-status badge-status--active">Activo</span>
                                    @else
                                        <span class="badge-status badge-status--inactive">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('micro.show', $micro->id) }}" class="btn btn-outline-brand btn-sm-action">
                                            <i class="bi bi-eye me-1" aria-hidden="true"></i>Ver
                                        </a>
                                        <a href="{{ route('micro.edit', $micro->id) }}" class="btn btn-outline-brand btn-sm-action">
                                            <i class="bi bi-pencil-square me-1" aria-hidden="true"></i>Editar
                                        </a>
                                        @if($micro->estado === 'activo')
                                            <form action="{{ route('micro.destroy', $micro->id) }}" method="POST" class="d-inline" data-confirm="¿Eliminar este micro?">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm-action">
                                                    <i class="bi bi-trash me-1" aria-hidden="true"></i>Eliminar
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <i class="bi bi-bus-front" aria-hidden="true"></i>
                                        <div class="empty-state-title">Sin micros registrados</div>
                                        <p class="mb-0">No se encontraron micros con los criterios actuales.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($micros->hasPages())
            <div class="card-footer">
                {{ $micros->appends(['buscar' => $buscar])->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
