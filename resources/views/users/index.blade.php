@extends('layouts.app')

@section('content')
<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 text-dark">Lista de Usuarios</h5>
            <a href="{{ route('users.create') }}" class="btn btn-sm fw-bold" style="background-color:#E07B15;border-color:#E07B15;color:white;">
                + Crear Nuevo Usuario
            </a>
        </div>
        <div class="card-body">
            <!-- Buscador -->
            <form method="GET" action="{{ route('users.index') }}" class="mb-3 d-flex gap-2">
                <input type="text" name="buscar" class="form-control form-control-sm" placeholder="Buscar por nombre o email..." value="{{ $buscar ?? '' }}">
                <button type="submit" class="btn btn-sm btn-secondary">Buscar</button>
                @if(!empty($buscar))
                    <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar</a>
                @endif
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Roles</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach($user->roles as $role)
                                        <span class="badge" style="background-color:#1A2D4F;">{{ $role->name }}</span>
                                    @endforeach
                                    @if($user->roles->isEmpty())
                                        <span class="text-muted small">Sin rol</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No se encontraron usuarios.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $users->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection