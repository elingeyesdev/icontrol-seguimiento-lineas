@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-dark">Editar Rol: {{ $role->name }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('roles.update', $role->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Nombre del Rol</label>
                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $role->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Permisos Asignados</label>
                            <div class="row">
                                @foreach($permissions as $permission)
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                   {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }} id="perm-{{ $permission->id }}">
                                            <label class="form-check-label text-muted" for="perm-{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('permissions')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn fw-bold" style="background-color:#E07B15; border-color:#E07B15; color:white;">
                                Actualizar Rol
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection