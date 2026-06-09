@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-dark fw-bold">Asignar Roles a: <span class="text-muted">{{ $user->name }}</span></h5>
                    <span class="badge bg-light text-dark border">{{ $user->email }}</span>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('users.roles.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label fw-bold mb-3" style="color: #1A2D4F;">Selecciona los Roles para el Usuario</label>
                            
                            <div class="row g-3">
                                @foreach($roles as $role)
                                    <div class="col-md-6">
                                        <div class="card h-100 border-0 bg-light p-3 position-relative transition-hover" style="border-radius: 8px;">
                                            <div class="form-check d-flex align-items-center mb-0">
                                                <input class="form-check-input me-3" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role_{{ $role->id }}"
                                                    {{ in_array($role->name, old('roles', $userRoles)) ? 'checked' : '' }}
                                                    style="width: 20px; height: 20px; cursor: pointer;">
                                                <label class="form-check-label fw-semibold text-dark flex-grow-1 cursor-pointer" for="role_{{ $role->id }}" style="cursor: pointer;">
                                                    {{ $role->name }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            @error('roles')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4 text-muted opacity-25">

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary px-4 fw-semibold">
                                <i class="bi bi-arrow-left me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn px-4 fw-bold shadow-sm" style="background-color:#E07B15; border-color:#E07B15; color:white;">
                                Guardar Roles
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .transition-hover {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .transition-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }
    .cursor-pointer {
        cursor: pointer;
    }
</style>
@endsection
