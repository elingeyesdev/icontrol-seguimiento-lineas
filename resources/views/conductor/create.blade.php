@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-dark">Crear Nuevo Conductor</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('conductor.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="nombre" class="form-label fw-bold">Nombre</label>
                            <input type="text" id="nombre" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="apellido" class="form-label fw-bold">Apellido</label>
                            <input type="text" id="apellido" name="apellido" class="form-control @error('apellido') is-invalid @enderror" value="{{ old('apellido') }}" required>
                            @error('apellido')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="telefono" class="form-label fw-bold">Teléfono</label>
                            <input type="text" id="telefono" name="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono') }}" required>
                            @error('telefono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="correo" class="form-label fw-bold">Correo Electrónico</label>
                            <input type="email" id="correo" name="correo" class="form-control @error('correo') is-invalid @enderror" value="{{ old('correo') }}" required>
                            @error('correo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="ci" class="form-label fw-bold">Cédula de Identidad (CI)</label>
                            <input type="text" id="ci" name="ci" class="form-control @error('ci') is-invalid @enderror" value="{{ old('ci') }}" required>
                            @error('ci')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('conductor.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn fw-bold" style="background-color:#E07B15; border-color:#E07B15; color:white;">
                                Guardar Conductor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection