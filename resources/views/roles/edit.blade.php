@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <nav aria-label="Ruta de navegación">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar rol</li>
        </ol>
    </nav>

    <div class="page-header">
        <div>
            <h1 class="page-title"><i class="bi bi-shield-check" aria-hidden="true"></i>Editar rol</h1>
            <p class="page-subtitle">Actualiza los datos y permisos de {{ $role->name }}.</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('roles.update', $role->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <fieldset class="form-section">
                            <legend class="form-section-title">Datos del rol</legend>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Nombre del rol <span class="form-hint-required">*</span></label>
                                    <input type="text" id="name" name="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $role->name) }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select id="estado" name="estado" class="form-select @error('estado') is-invalid @enderror">
                                        <option value="activo" {{ old('estado', $role->estado ?? 'activo') === 'activo' ? 'selected' : '' }}>Activo</option>
                                        <option value="inactivo" {{ old('estado', $role->estado ?? 'activo') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                    @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea id="descripcion" name="descripcion" rows="2"
                                              class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion', $role->descripcion) }}</textarea>
                                    @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="form-section">
                            <legend class="form-section-title">Permisos</legend>
                            <div class="row g-2">
                                @foreach($permissions as $permission)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]"
                                                   value="{{ $permission->name }}" id="perm-{{ $permission->id }}"
                                                   {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="perm-{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('permissions')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                        </fieldset>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-brand">
                                <i class="bi bi-check-lg me-1" aria-hidden="true"></i>Actualizar rol
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
