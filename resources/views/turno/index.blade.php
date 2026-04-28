@extends('layouts.app')

@section('content')
<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 text-dark">Lista de Turnos</h5>
            <a href="{{ route('turno.create') }}" class="btn btn-sm fw-bold" style="background-color:#E07B15;border-color:#E07B15;color:white;">
                + Crear Nuevo Turno
            </a>
        </div>
        <div class="card-body">
            <!-- Buscador -->
            <form method="GET" action="{{ route('turno.index') }}" class="mb-3 d-flex gap-2">
                <input type="text" name="buscar" class="form-control form-control-sm" placeholder="Buscar por fecha laboral..." value="{{ $buscar ?? '' }}">
                <button type="submit" class="btn btn-sm btn-secondary">Buscar</button>
                @if(!empty($buscar))
                    <a href="{{ route('turno.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar</a>
                @endif
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Interno ID</th>
                            <th>Ruta ID</th>
                            <th>Hora Inicio</th>
                            <th>Hora Fin</th>
                            <th>Fecha Laboral</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($turnos as $turno)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $turno->interno_id }}</td>
                                <td>{{ $turno->ruta_id }}</td>
                                <td>{{ $turno->hora_inicio }}</td>
                                <td>{{ $turno->hora_fin }}</td>
                                <td>{{ $turno->fecha_laboral }}</td>
                                <td>
                                    <a href="{{ route('turno.show', $turno->id) }}" class="btn btn-sm btn-outline-info">Ver</a>
                                    <a href="{{ route('turno.edit', $turno->id) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                                    <form action="{{ route('turno.destroy', $turno->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('¿Seguro que deseas eliminar este turno?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No se encontraron turnos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $turnos->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
