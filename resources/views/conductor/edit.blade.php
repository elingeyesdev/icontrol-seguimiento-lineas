@extends('layouts.app')
@section('content')
@vite('resources/js/catalogos.js')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('conductor.index') }}" class="text-decoration-none">Conductores</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar: {{ $conductor->nombre }} {{ $conductor->apellido }}</li>
                </ol>
            </nav>

            <div class="page-header">
                <div>
                    <h1 class="page-title"><i class="bi bi-person-badge" aria-hidden="true"></i>Editar Conductor</h1>
                    <p class="page-subtitle">Actualice los datos del conductor.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('conductor.update', $conductor->id) }}">
                @csrf @method('PUT')
                <div class="card">
                    <div class="card-body">
                        <div class="form-section">
                            <div class="form-section-title">Cuenta vinculada</div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="user_id" class="form-label">Usuario de acceso</label>
                                    <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror">
                                        <option value="">Sin cuenta vinculada</option>
                                        @foreach($usuarios as $usuario)
                                            <option value="{{ $usuario->id }}" data-nombre="{{ $usuario->name }}" data-apellido="{{ $usuario->apellido }}" data-ci="{{ $usuario->ci }}" data-telefono="{{ $usuario->telefono }}" data-correo="{{ $usuario->email }}" {{ old('user_id', $conductor->user_id) == $usuario->id ? 'selected' : '' }}>
                                                {{ $usuario->name }} — {{ $usuario->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Un usuario también puede estar vinculado como propietario.</div>
                                    @error('user_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="licencia" class="form-label">Licencia de conducir</label>
                                    <input type="text" name="licencia" id="licencia" class="form-control @error('licencia') is-invalid @enderror" value="{{ old('licencia', $conductor->licencia) }}">
                                    @error('licencia')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="form-section-title">Datos personales</div>
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label for="nombre" class="form-label">Nombre <span class="form-hint-required">*</span></label>
                                    <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $conductor->nombre) }}" readonly>
                                    @error('nombre')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="apellido" class="form-label">Apellido <span class="form-hint-required">*</span></label>
                                    <input type="text" name="apellido" id="apellido" class="form-control @error('apellido') is-invalid @enderror" value="{{ old('apellido', $conductor->apellido) }}" readonly>
                                    @error('apellido')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="ci" class="form-label">CI <span class="form-hint-required">*</span></label>
                                    <input type="text" name="ci" id="ci" class="form-control @error('ci') is-invalid @enderror" value="{{ old('ci', $conductor->ci) }}" readonly>
                                    @error('ci')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-md-8">
                                    <label for="telefono" class="form-label">Teléfono <span class="form-hint-required">*</span></label>
                                    <input type="text" name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono', $conductor->telefono) }}" readonly>
                                    @error('telefono')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="form-section-title">Contacto y estado</div>
                            <div class="row g-3">
                                <div class="col-12 col-md-8">
                                    <label for="correo" class="form-label">Correo electrónico <span class="form-hint-required">*</span></label>
                                    <input type="email" name="correo" id="correo" class="form-control @error('correo') is-invalid @enderror" value="{{ old('correo', $conductor->correo) }}" readonly>
                                    @error('correo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="estado" class="form-label">Estado <span class="form-hint-required">*</span></label>
                                    <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror">
                                        <option value="activo" {{ old('estado', $conductor->estado ?? 'activo') === 'activo' ? 'selected' : '' }}>Activo</option>
                                        <option value="inactivo" {{ old('estado', $conductor->estado) === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                    @error('estado')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <a href="{{ route('conductor.index') }}" class="btn btn-outline-brand">
                            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-brand">
                            <i class="bi bi-check-lg me-1" aria-hidden="true"></i>Actualizar Conductor
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
