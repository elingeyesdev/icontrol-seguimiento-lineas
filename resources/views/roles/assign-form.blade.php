@extends('layouts.app')

@section('content')
@vite('resources/js/roles.js')
<div class="container-fluid py-4">
    <nav aria-label="Ruta de navegación">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
            <li class="breadcrumb-item"><a href="{{ route('roles.assign') }}">Asignaciones</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ isset($edit_user) ? 'Modificar asignación' : 'Registrar asignación' }}</li>
        </ol>
    </nav>

    <div class="page-header">
        <div>
            <h1 class="page-title"><i class="bi bi-person-gear" aria-hidden="true"></i>
                {{ isset($edit_user) ? 'Modificar asignación de roles' : 'Registrar asignación de roles' }}
            </h1>
            <p class="page-subtitle">Selecciona un usuario y define los roles que tendrá asignados.</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('roles.assign.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="user_id" class="form-label">
                                Usuario <span class="form-hint-required">*</span>
                            </label>
                            <select name="user_id" id="user_id"
                                    class="form-select @error('user_id') is-invalid @enderror"
                                    required>
                                <option value="">Selecciona un usuario</option>
                                @foreach($allUsers as $u)
                                    <option value="{{ $u->id }}" {{ old('user_id', $edit_user->id ?? '') == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }} ({{ $u->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-end mb-2">
                            <div>
                                <span class="form-label d-block mb-0">Roles a asignar</span>
                                <p class="form-text mb-0">Puedes seleccionar uno o varios roles permitidos.</p>
                            </div>
                        </div>

                        <div class="row g-2">
                            @foreach($roles as $role)
                                @php
                                    $selectedRoles = old('roles', isset($edit_user) ? $edit_user->roles->pluck('name')->toArray() : []);
                                    $isChecked = in_array($role->name, $selectedRoles, true);
                                    $roleIcon = match (strtolower($role->name)) {
                                        'admin', 'administrador' => 'bi-shield',
                                        'conductor' => 'bi-car-front',
                                        'fiscalizador' => 'bi-shield-check',
                                        'propietario' => 'bi-house',
                                        default => 'bi-person-badge',
                                    };
                                @endphp
                                <div class="col-12 col-md-6">
                                    <label class="role-option {{ $isChecked ? 'selected' : '' }}" for="role_{{ $role->id }}">
                                        <input class="role-option-input" type="checkbox" name="roles[]"
                                               value="{{ $role->name }}" id="role_{{ $role->id }}"
                                               {{ $isChecked ? 'checked' : '' }}>
                                        <span class="role-option-check"><i class="bi bi-check" aria-hidden="true"></i></span>
                                        <span class="role-option-icon"><i class="bi {{ $roleIcon }}" aria-hidden="true"></i></span>
                                        <span class="role-option-name">{{ $role->name }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        @error('roles')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                        @error('roles.*')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror

                        <div class="role-selection-summary mt-3">
                            <i class="bi bi-check-circle-fill me-1" aria-hidden="true"></i>
                            <span><strong id="selected-role-count">0</strong> <span id="selected-role-label">roles seleccionados</span></span>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('roles.assign') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-brand">
                                <i class="bi bi-check-lg me-1" aria-hidden="true"></i>{{ isset($edit_user) ? 'Actualizar asignación' : 'Guardar asignación' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
