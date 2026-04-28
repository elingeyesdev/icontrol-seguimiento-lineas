@extends('layouts.app')

@section('content')
<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 text-dark">Lista de Micros</h5>
            <a href="{{ route('micro.create') }}" class="btn btn-sm fw-bold" style="background-color:#E07B15;border-color:#E07B15;color:white;">
                + Crear Nuevo Micro
            </a>
        </div>
        <div class="card-body">
            <!-- Buscador -->
            <form method="GET" action="{{ route('micro.index') }}" class="mb-3 d-flex gap-2">
                <input type="text" name="buscar" class="form-control form-control-sm" placeholder="Buscar por placa, modelo o marca..." value="{{ $buscar ?? '' }}">
                <button type="submit" class="btn btn-sm btn-secondary">Buscar</button>
                @if(!empty($buscar))
                    <a href="{{ route('micro.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar</a>
                @endif
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Dueño</th>
                            <th>Placa</th>
                            <th>Modelo</th>
                            <th>Marca</th>
                            <th>Capacidad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($micros as $micro)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $micro->dueño->nombre ?? $micro->dueño_id }}</td>
                                <td>{{ $micro->placa }}</td>
                                <td>{{ $micro->modelo }}</td>
                                <td>{{ $micro->marca }}</td>
                                <td>{{ $micro->capacidad_pasajeros }}</td>
                                <td>
                                    <a href="{{ route('micro.show', $micro->id) }}" class="btn btn-sm btn-outline-info">Ver</a>
                                    <a href="{{ route('micro.edit', $micro->id) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                                    <form action="{{ route('micro.destroy', $micro->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('¿Seguro que deseas eliminar este micro?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No se encontraron micros.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $micros->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
