@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 text-dark">Detalles de la Ruta</h5>
                    <a href="{{ route('ruta.index') }}" class="btn btn-sm btn-outline-secondary">Volver</a>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Origen:</div>
                        <div class="col-sm-8 text-muted">{{ $ruta->origen }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Destino:</div>
                        <div class="col-sm-8 text-muted">{{ $ruta->destino }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Nombre Ruta:</div>
                        <div class="col-sm-8 text-muted">{{ $ruta->nombre_ruta }}</div>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <a href="{{ route('ruta.edit', $ruta->id) }}" class="btn btn-warning btn-sm">Editar</a>
                        <form action="{{ route('ruta.destroy', $ruta->id) }}" method="POST" class="d-inline">
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
