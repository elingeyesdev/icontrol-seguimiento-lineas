@extends('layouts.app')

@section('content')
<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 text-dark">Lista de Internos</h5>
            <a href="{{ route('interno.create') }}" class="btn btn-sm fw-bold" style="background-color:#E07B15;border-color:#E07B15;color:white;">
                + Crear Nuevo Interno
            </a>
        </div>
        <div class="card-body">
            <!-- Buscador -->
            <form method="GET" action="{{ route('interno.index') }}" class="mb-3 d-flex gap-2">
                <input type="text" name="buscar" class="form-control form-control-sm" placeholder="Buscar por estado..." value="{{ $buscar ?? '' }}">
                <button type="submit" class="btn btn-sm btn-secondary">Buscar</button>
                @if(!empty($buscar))
                    <a href="{{ route('interno.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar</a>
                @endif
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Estado</th>
                            <th>Micro</th>
                            <th>Conductor</th>
                            <th>Fecha Ingreso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($internos as $interno)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $interno->estado }}</td>
                                <td>{{ $interno->micro_id }}</td>
                                <td>{{ $interno->conductor_id }}</td>
                                <td>{{ $interno->fecha_ingreso }}</td>
                                <td>
                                    <a href="{{ route('interno.show', $interno->id) }}" class="btn btn-sm btn-outline-info">Ver</a>
                                    <a href="{{ route('interno.edit', $interno->id) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                                    <form action="{{ route('interno.destroy', $interno->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('¿Seguro que deseas eliminar este interno?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No se encontraron internos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $internos->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
