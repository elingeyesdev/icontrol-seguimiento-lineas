@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 text-dark">Detalles del Dueño</h5>
                    <a href="{{ route('dueño.index') }}" class="btn btn-sm btn-outline-secondary">Volver</a>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Nombre:</div>
                        <div class="col-sm-8 text-muted">{{ $dueño->nombre }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Apellido:</div>
                        <div class="col-sm-8 text-muted">{{ $dueño->apellido }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Teléfono:</div>
                        <div class="col-sm-8 text-muted">{{ $dueño->telefono }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Correo:</div>
                        <div class="col-sm-8 text-muted">{{ $dueño->correo }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">CI:</div>
                        <div class="col-sm-8 text-muted">{{ $dueño->ci }}</div>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <a href="{{ route('dueño.edit', $dueño->id) }}" class="btn btn-warning btn-sm">Editar</a>
                        <form action="{{ route('dueño.destroy', $dueño->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar?')">Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
