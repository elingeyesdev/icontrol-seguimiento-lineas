@extends('layouts.app')

@section('content')
<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 text-dark">Lista de Rutas</h5>
            <a href="{{ route('ruta.create') }}" class="btn btn-sm fw-bold" style="background-color:#E07B15;border-color:#E07B15;color:white;">
                + Crear Nueva Ruta
            </a>
        </div>
        <div class="card-body">
            <!-- Buscador -->
            <form method="GET" action="{{ route('ruta.index') }}" class="mb-3 d-flex gap-2">
                <input type="text" name="buscar" class="form-control form-control-sm" placeholder="Buscar por origen, destino o nombre..." value="{{ $buscar ?? '' }}">
                <button type="submit" class="btn btn-sm btn-secondary">Buscar</button>
                @if(!empty($buscar))
                    <a href="{{ route('ruta.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar</a>
                @endif
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Nombre Ruta</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rutas as $ruta)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $ruta->origen }}</td>
                                <td>{{ $ruta->destino }}</td>
                                <td>{{ $ruta->nombre_ruta }}</td>
                                <td>
                                    <a href="{{ route('ruta.show', $ruta->id) }}" class="btn btn-sm btn-outline-info">Ver</a>
                                    <a href="{{ route('ruta.edit', $ruta->id) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                                    <form action="{{ route('ruta.destroy', $ruta->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('¿Seguro que deseas eliminar esta ruta?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No se encontraron rutas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $rutas->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
