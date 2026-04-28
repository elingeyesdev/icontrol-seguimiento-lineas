@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 text-dark">Detalles del Micro</h5>
                    <a href="{{ route('micro.index') }}" class="btn btn-sm btn-outline-secondary">Volver</a>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Dueño ID:</div>
                        <div class="col-sm-8 text-muted">{{ $micro->dueño_id }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Placa:</div>
                        <div class="col-sm-8 text-muted">{{ $micro->placa }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Modelo:</div>
                        <div class="col-sm-8 text-muted">{{ $micro->modelo }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Marca:</div>
                        <div class="col-sm-8 text-muted">{{ $micro->marca }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Capacidad Pasajeros:</div>
                        <div class="col-sm-8 text-muted">{{ $micro->capacidad_pasajeros }}</div>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <a href="{{ route('micro.edit', $micro->id) }}" class="btn btn-warning btn-sm">Editar</a>
                        <form action="{{ route('micro.destroy', $micro->id) }}" method="POST" class="d-inline">
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
