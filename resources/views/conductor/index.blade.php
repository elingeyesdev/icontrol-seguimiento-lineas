@extends('layouts.app')

@section('content')
<div class="container py-4">
    @if(session('info'))
        <div class="alert alert-success">{{ session('info') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 text-dark">Lista de Conductores</h5>
            @can('conductor.create')
                <a href="{{ route('conductor.create') }}" class="btn btn-sm fw-bold" style="background-color:#E07B15;border-color:#E07B15;color:white;">
                    + Crear Nuevo Conductor
                </a>
            @endcan
        </div>
        <div class="card-body">
            <!-- Buscador -->
            <form method="GET" action="{{ route('conductor.index') }}" class="mb-3 d-flex gap-2">
                <input type="text" name="buscar" class="form-control form-control-sm" placeholder="Buscar por nombre, apellido, correo o CI..." value="{{ $buscar ?? '' }}">
                <button type="submit" class="btn btn-sm btn-secondary">Buscar</button>
                @if(!empty($buscar))
                    <a href="{{ route('conductor.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar</a>
                @endif
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>CI</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($conductores as $conductor)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $conductor->nombre }}</td>
                                <td>{{ $conductor->apellido }}</td>
                                <td>{{ $conductor->telefono }}</td>
                                <td>{{ $conductor->correo }}</td>
                                <td>{{ $conductor->ci }}</td>
                                <td>
                                    @can('conductor.show')
                                        <a href="{{ route('conductor.show', $conductor->id) }}" class="btn btn-sm btn-outline-info">Ver</a>
                                    @endcan
                                    @can('conductor.edit')
                                        <a href="{{ route('conductor.edit', $conductor->id) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                                    @endcan
                                    @can('conductor.destroy')
                                        <form action="{{ route('conductor.destroy', $conductor->id) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('¿Seguro que deseas eliminar este conductor?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No se encontraron conductores.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $conductores->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection