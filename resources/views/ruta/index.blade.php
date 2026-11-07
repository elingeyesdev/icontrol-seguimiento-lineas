@extends('layouts.app')

@section('content')
@vite('resources/css/catalogos.css')
<div class="container-fluid py-4">
    <div class="page-header">
        <div>
            <h1 class="page-title"><i class="bi bi-signpost-2" aria-hidden="true"></i>Gestión de Rutas</h1>
            <p class="page-subtitle">Administra las rutas del sistema de transporte.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('ruta.create') }}" class="btn btn-brand">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Registrar Ruta
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
            <form method="GET" action="{{ route('ruta.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-6 col-lg-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="text" name="buscar" class="form-control" placeholder="Buscar rutas" value="{{ $buscar ?? '' }}" aria-label="Buscar rutas">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-outline-brand">Buscar</button>
                </div>
                @if(!empty($buscar))
                    <div class="col-auto">
                        <a href="{{ route('ruta.index') }}" class="btn btn-outline-brand btn-icon" aria-label="Limpiar búsqueda">
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
                            <th>No</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Paradas (Ida + Vuelta)</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rutas as $ruta)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration + ($rutas->currentPage() - 1) * $rutas->perPage() }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="avatar avatar--wine"><i class="bi bi-signpost-2" aria-hidden="true"></i></span>
                                        <span class="fw-semibold">{{ $ruta->nombre }}</span>
                                    </div>
                                </td>
                                <td class="text-muted">{{ Str::limit($ruta->descripcion ?? '—', 60) }}</td>
                                <td>
                                    @if($ruta->estado === 'activo')
                                        <span class="badge-status badge-status--active">Activo</span>
                                    @else
                                        <span class="badge-status badge-status--inactive">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-brand-subtle text-brand">
                                        <i class="bi bi-geo-alt me-1" aria-hidden="true"></i>{{ $ruta->paradas_count }} paradas
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('ruta.show', $ruta->id) }}" class="btn btn-outline-brand btn-sm-action">
                                            <i class="bi bi-eye me-1" aria-hidden="true"></i>Ver
                                        </a>
                                        <a href="{{ route('ruta.edit', $ruta->id) }}" class="btn btn-outline-brand btn-sm-action">
                                            <i class="bi bi-pencil-square me-1" aria-hidden="true"></i>Editar
                                        </a>
                                        @if($ruta->estado === 'activo')
                                            <form action="{{ route('ruta.destroy', $ruta->id) }}" method="POST" class="d-inline" data-confirm="¿Eliminar esta ruta?">
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
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="bi bi-signpost-2" aria-hidden="true"></i>
                                        <div class="empty-state-title">Sin rutas registradas</div>
                                        <p class="mb-2">No se encontraron rutas con los criterios actuales.</p>
                                        <a href="{{ route('ruta.create') }}" class="btn btn-outline-brand btn-sm-action">
                                            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Crear primera ruta
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($rutas->hasPages())
            <div class="card-footer">
                {{ $rutas->appends(['buscar' => $buscar])->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
