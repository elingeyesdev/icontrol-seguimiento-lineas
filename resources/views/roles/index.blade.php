@extends('layouts.app')

@section('content')
<div class="container py-4">
    @if(session('info'))
        <div class="alert alert-success">{{ session('info') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 text-dark">Lista de Roles</h5>
            <a href="{{ route('roles.create') }}" class="btn btn-sm fw-bold" style="background-color:#E07B15;border-color:#E07B15;color:white;">
                + Crear Nuevo Rol
            </a>
        </div>
        <div class="card-body">
            <!-- Buscador -->
            <form method="GET" action="{{ route('roles.index') }}" class="mb-3 d-flex gap-2">
                <input type="text" name="buscar" class="form-control form-control-sm" placeholder="Buscar rol por nombre..." value="{{ $buscar ?? '' }}">
                <button type="submit" class="btn btn-sm btn-secondary">Buscar</button>
                @if(!empty($buscar))
                    <a href="{{ route('roles.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar</a>
                @endif
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Permisos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><span class="badge" style="background-color:#1A2D4F;">{{ $role->name }}</span></td>
                                <td>{{ $role->permissions->pluck('name')->join(', ') }}</td>
                                <td>
                                    <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('¿Seguro que deseas eliminar este rol?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No se encontraron roles.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $roles->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection