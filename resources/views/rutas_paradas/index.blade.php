@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="page-header">
        <div>
            <h1 class="page-title"><i class="bi bi-diagram-3" aria-hidden="true"></i>Rutas y Paradas</h1>
            <p class="page-subtitle">Asociación de paradas a rutas (relación M:N).</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('rutas-paradas.create') }}" class="btn btn-brand">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Nueva Asociación
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
            <form method="GET" action="{{ route('rutas-paradas.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-6 col-lg-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="text" name="buscar" class="form-control" placeholder="Buscar por ruta o parada" value="{{ $buscar ?? '' }}" aria-label="Buscar asociaciones">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-outline-brand">Buscar</button>
                </div>
                @if(!empty($buscar))
                    <div class="col-auto">
                        <a href="{{ route('rutas-paradas.index') }}" class="btn btn-outline-brand">Limpiar</a>
                    </div>
                @endif
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Ruta</th>
                            <th>Parada</th>
                            <th>Orden</th>
                            <th>Sentido</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rutasParadas as $rp)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    @if($rp->ruta)
                                        <span class="fw-semibold">{{ $rp->ruta->nombre }}</span>
                                        @if($rp->ruta->descripcion)
                                            <span class="text-muted d-block small">{{ $rp->ruta->descripcion }}</span>
                                        @endif
                                    @else
                                        <span class="text-danger">Ruta eliminada</span>
                                    @endif
                                </td>
                                <td>
                                    @if($rp->parada)
                                        {{ $rp->parada->nombre }}
                                    @else
                                        <span class="text-danger">Parada eliminada</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $rp->orden }}</td>
                                <td><span class="badge badge-status--inactive">{{ ucfirst($rp->sentido) }}</span></td>
                                <td>
                                    <span class="badge-status {{ $rp->estado === 'activo' ? 'badge-status--active' : 'badge-status--inactive' }}">
                                        {{ ucfirst($rp->estado) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('rutas-paradas.show', $rp->id) }}" class="btn btn-outline-brand btn-sm-action">
                                            <i class="bi bi-eye me-1" aria-hidden="true"></i>Ver
                                        </a>
                                        <a href="{{ route('rutas-paradas.edit', $rp->id) }}" class="btn btn-outline-brand btn-sm-action">
                                            <i class="bi bi-pencil-square me-1" aria-hidden="true"></i>Editar
                                        </a>
                                        @if($rp->estado === 'activo')
                                            <form action="{{ route('rutas-paradas.destroy', $rp->id) }}" method="POST" class="d-inline" data-confirm="¿Eliminar esta asociación?">
                                                @csrf
                                                @method('DELETE')
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
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="bi bi-diagram-3" aria-hidden="true"></i>
                                        <div class="empty-state-title">Sin asociaciones registradas</div>
                                        <p class="mb-0">No se encontraron asociaciones con los criterios actuales.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $rutasParadas->links() }}
        </div>
    </div>
</div>
@endsection
