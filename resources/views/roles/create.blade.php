@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <nav aria-label="Ruta de navegación">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
            <li class="breadcrumb-item active" aria-current="page">Registrar rol</li>
        </ol>
    </nav>

    <div class="page-header">
        <div>
            <h1 class="page-title"><i class="bi bi-shield-plus" aria-hidden="true"></i>Registrar rol</h1>
            <p class="page-subtitle">Define el nombre, estado y descripción del nuevo rol.</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('roles.store') }}" method="POST">
                        @csrf
                        <fieldset class="form-section">
                            <legend class="form-section-title">Datos del rol</legend>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Nombre del rol <span class="form-hint-required">*</span></label>
                                    <input type="text" id="name" name="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}" placeholder="Ej: Administrador" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="estado" class="form-label">Estado <span class="form-hint-required">*</span></label>
                                    <select id="estado" name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                                        <option value="activo" {{ old('estado') === 'activo' ? 'selected' : '' }}>Activo</option>
                                        <option value="inactivo" {{ old('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                    @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea id="descripcion" name="descripcion" rows="3"
                                              class="form-control @error('descripcion') is-invalid @enderror"
                                              placeholder="Describe brevemente la función de este rol">{{ old('descripcion') }}</textarea>
                                    @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </fieldset>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-brand">
                                <i class="bi bi-check-lg me-1" aria-hidden="true"></i>Guardar rol
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
