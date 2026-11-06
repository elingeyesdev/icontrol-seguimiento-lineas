@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="page-header">
        <div>
            <h1 class="page-title"><i class="bi bi-person-vcard" aria-hidden="true"></i>Gestión de Propietarios</h1>
            <p class="page-subtitle">Administra los propietarios del sistema.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('propietario.create') }}" class="btn btn-brand">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Registrar Propietario
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
            <form method="GET" action="{{ route('propietario.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-6 col-lg-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre, CI o correo" value="{{ $buscar ?? '' }}" aria-label="Buscar propietarios">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-outline-brand">Buscar</button>
                </div>
                @if(!empty($buscar))
                    <div class="col-auto">
                        <a href="{{ route('propietario.index') }}" class="btn btn-outline-brand btn-icon" aria-label="Limpiar búsqueda">
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
                            <th>Propietario</th>
                            <th>CI</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($propietarios as $propietario)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration + ($propietarios->currentPage() - 1) * $propietarios->perPage() }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="avatar">{{ strtoupper(substr($propietario->nombre, 0, 1)) }}{{ strtoupper(substr($propietario->apellido, 0, 1)) }}</span>
                                        <span class="fw-semibold">{{ $propietario->nombre }} {{ $propietario->apellido }}</span>
                                    </div>
                                </td>
                                <td class="text-muted mono">{{ $propietario->ci }}</td>
                                <td class="text-muted">{{ $propietario->telefono ?? '—' }}</td>
                                <td class="text-muted">{{ $propietario->correo }}</td>
                                <td>
                                    @if(($propietario->estado ?? 'activo') === 'activo')
                                        <span class="badge-status badge-status--active">Activo</span>
                                    @else
                                        <span class="badge-status badge-status--inactive">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('propietario.show', $propietario->id) }}" class="btn btn-outline-brand btn-sm-action">
                                            <i class="bi bi-eye me-1" aria-hidden="true"></i>Ver
                                        </a>
                                        <a href="{{ route('propietario.edit', $propietario->id) }}" class="btn btn-outline-brand btn-sm-action">
                                            <i class="bi bi-pencil-square me-1" aria-hidden="true"></i>Editar
                                        </a>
                                        @if(($propietario->estado ?? 'activo') === 'activo')
                                            <form action="{{ route('propietario.destroy', $propietario->id) }}" method="POST" class="d-inline" data-confirm="¿Eliminar este propietario?">
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
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="bi bi-person-vcard" aria-hidden="true"></i>
                                        <div class="empty-state-title">Sin propietarios registrados</div>
                                        <p class="mb-0">No se encontraron propietarios con los criterios actuales.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($propietarios->hasPages())
            <div class="card-footer">
                {{ $propietarios->appends(['buscar' => $buscar])->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
