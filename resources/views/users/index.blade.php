@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="page-header">
        <div>
            <h1 class="page-title"><i class="bi bi-people" aria-hidden="true"></i>Usuarios</h1>
            <p class="page-subtitle">Administra las cuentas de acceso y sus roles en el sistema.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('users.create') }}" class="btn btn-brand">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Registrar usuario
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

    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route('users.index') }}" class="d-flex flex-wrap gap-2 align-items-center">
                <div class="input-group search-field">
                    <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                    <input type="search" name="buscar" class="form-control" placeholder="Buscar por nombre o correo"
                           value="{{ $buscar ?? '' }}" aria-label="Buscar usuarios">
                </div>
                <button type="submit" class="btn btn-outline-brand">Buscar</button>
                @if(!empty($buscar))
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
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
                            <th scope="col">Correo</th>
                            <th scope="col">Roles</th>
                            <th scope="col" class="text-end pe-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="ps-3 text-muted">{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="avatar" aria-hidden="true">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                        <span class="fw-semibold">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="text-muted">{{ $user->email }}</td>
                                <td>
                                    @forelse($user->roles as $role)
                                        <span class="badge-role me-1">{{ $role->name }}</span>
                                    @empty
                                        <span class="text-muted small">Sin rol asignado</span>
                                    @endforelse
                                </td>
                                <td class="text-end pe-3">
                                    <div class="table-actions">
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-outline-brand btn-sm-action">
                                            <i class="bi bi-pencil-square me-1" aria-hidden="true"></i>Editar
                                        </a>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline"
                                              data-confirm="¿Eliminar este usuario?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm-action">
                                                <i class="bi bi-trash me-1" aria-hidden="true"></i>Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="bi bi-people" aria-hidden="true"></i>
                                        <p class="empty-state-title mb-1">No se encontraron usuarios</p>
                                        <p class="mb-0">Ajusta la búsqueda o registra una nueva cuenta.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($users->hasPages())
            <div class="card-footer">
                {{ $users->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
