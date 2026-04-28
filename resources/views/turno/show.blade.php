@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 text-dark">Detalles del Turno</h5>
                    <a href="{{ route('turno.index') }}" class="btn btn-sm btn-outline-secondary">Volver</a>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Interno ID:</div>
                        <div class="col-sm-8 text-muted">{{ $turno->interno_id }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Ruta ID:</div>
                        <div class="col-sm-8 text-muted">{{ $turno->ruta_id }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Hora Inicio:</div>
                        <div class="col-sm-8 text-muted">{{ $turno->hora_inicio }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Hora Fin:</div>
                        <div class="col-sm-8 text-muted">{{ $turno->hora_fin }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Fecha Laboral:</div>
                        <div class="col-sm-8 text-muted">{{ $turno->fecha_laboral }}</div>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <a href="{{ route('turno.edit', $turno->id) }}" class="btn btn-warning btn-sm">Editar</a>
                        <form action="{{ route('turno.destroy', $turno->id) }}" method="POST" class="d-inline">
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
