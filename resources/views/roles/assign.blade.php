@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="page-header">
        <div>
            <h1 class="page-title"><i class="bi bi-person-gear" aria-hidden="true"></i>Asignación usuario · rol</h1>
            <p class="page-subtitle">Consulta y administra los roles asignados a cada usuario.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('roles.index') }}" class="btn btn-outline-brand">
                <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Volver a roles
            </a>
            <a href="{{ route('roles.assign.create') }}" class="btn btn-brand">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Registrar asignación
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
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h2 class="section-title fs-6">Asignaciones registradas</h2>
            <form method="GET" action="{{ route('roles.assign') }}" class="d-flex flex-wrap gap-2 align-items-center">
                <div class="input-group search-field-sm">
                    <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                    <input type="search" name="buscar" class="form-control form-control-sm"
                           placeholder="Buscar por usuario o correo" value="{{ $buscar ?? '' }}"
                           aria-label="Buscar asignaciones">
                </div>
                <button type="submit" class="btn btn-outline-brand btn-sm">Buscar</button>
                @if(!empty($buscar))
                    <a href="{{ route('roles.assign') }}" class="btn btn-outline-secondary btn-sm" aria-label="Limpiar búsqueda">
                        <i class="bi bi-x-lg" aria-hidden="true"></i>
                    </a>
                @endif
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col" class="ps-3">Usuario</th>
                            <th scope="col">Roles asignados</th>
                            <th scope="col" class="text-end pe-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="avatar" aria-hidden="true">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                        <span>
                                            <span class="fw-semibold d-block">{{ $user->name }}</span>
                                            <span class="text-muted small">{{ $user->email }}</span>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    @forelse($user->roles as $role)
                                        <span class="badge-role me-1">{{ $role->name }}</span>
                                    @empty
                                        <span class="badge-status badge-status--inactive">Sin roles</span>
                                    @endforelse
                                </td>
                                <td class="text-end pe-3">
                                    <div class="table-actions">
                                        <a href="{{ route('roles.assign.edit', $user->id) }}" class="btn btn-outline-brand btn-sm-action">
                                            <i class="bi bi-pencil-square me-1" aria-hidden="true"></i>Modificar
                                        </a>
                                        @if($user->roles->count() > 0)
                                            <form action="{{ route('roles.assign.destroy', $user->id) }}" method="POST" class="d-inline"
                                                  data-confirm="¿Quitar todos los roles a este usuario?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm-action">
                                                    <i class="bi bi-trash me-1" aria-hidden="true"></i>Quitar roles
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    <div class="empty-state">
                                        <i class="bi bi-people" aria-hidden="true"></i>
                                        <p class="empty-state-title mb-1">No se encontraron usuarios</p>
                                        <p class="mb-0">Ajusta la búsqueda para ver otras asignaciones.</p>
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
