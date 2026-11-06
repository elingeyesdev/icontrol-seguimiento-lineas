@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="page-header">
        <div>
            <h1 class="page-title"><i class="bi bi-shield-lock" aria-hidden="true"></i>Roles</h1>
            <p class="page-subtitle">Define los roles disponibles y sus permisos dentro del sistema.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('roles.assign') }}" class="btn btn-outline-brand">
                <i class="bi bi-person-gear me-1" aria-hidden="true"></i>Asignar roles
            </a>
            <a href="{{ route('roles.create') }}" class="btn btn-brand">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Registrar rol
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-soft-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar aviso"></button>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-soft-info alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
            <div>{{ session('info') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar aviso"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route('roles.index') }}" class="d-flex flex-wrap gap-2 align-items-center">
                <div class="input-group search-field">
                    <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                    <input type="search" name="buscar" class="form-control" placeholder="Buscar por nombre"
                           value="{{ $buscar ?? '' }}" aria-label="Buscar roles">
                </div>
                <button type="submit" class="btn btn-outline-brand">Buscar</button>
                @if(!empty($buscar))
                    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1" aria-hidden="true"></i>Limpiar
                    </a>
                @endif
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col" class="ps-3">#</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Descripción</th>
                            <th scope="col">Estado</th>
                            <th scope="col" class="text-end pe-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td class="ps-3 text-muted">{{ $loop->iteration + ($roles->currentPage() - 1) * $roles->perPage() }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="avatar avatar--wine" aria-hidden="true">{{ strtoupper(substr($role->name, 0, 2)) }}</span>
                                        <span class="fw-semibold">{{ $role->name }}</span>
                                    </div>
                                </td>
                                <td class="text-muted">{{ $role->descripcion ?? 'Sin descripción' }}</td>
                                <td>
                                    @if(($role->estado ?? 'activo') === 'activo')
                                        <span class="badge-status badge-status--active"><i class="bi bi-check-circle" aria-hidden="true"></i>Activo</span>
                                    @else
                                        <span class="badge-status badge-status--inactive"><i class="bi bi-dash-circle" aria-hidden="true"></i>Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="table-actions">
                                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-outline-brand btn-sm-action">
                                            <i class="bi bi-pencil-square me-1" aria-hidden="true"></i>Editar
                                        </a>
                                        @if(($role->estado ?? 'activo') === 'activo')
                                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline"
                                                  data-confirm="¿Eliminar este rol?">
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
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="bi bi-shield-lock" aria-hidden="true"></i>
                                        <p class="empty-state-title mb-1">No se encontraron roles</p>
                                        <p class="mb-0">Ajusta la búsqueda o registra un nuevo rol.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($roles->hasPages())
            <div class="card-footer">
                {{ $roles->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
