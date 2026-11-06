@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <nav aria-label="Ruta de navegación">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuarios</a></li>
            <li class="breadcrumb-item active" aria-current="page">Registrar usuario</li>
        </ol>
    </nav>

    <div class="page-header">
        <div>
            <h1 class="page-title"><i class="bi bi-person-plus" aria-hidden="true"></i>Registrar usuario</h1>
            <p class="page-subtitle">Crea una cuenta de acceso con sus datos de identificación.</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf

                        <fieldset class="form-section">
                            <legend class="form-section-title">Datos personales</legend>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Nombre completo <span class="form-hint-required">*</span></label>
                                    <input type="text" id="name" name="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}" required autocomplete="name">
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="apellido" class="form-label">Apellido <span class="form-hint-required">*</span></label>
                                    <input type="text" id="apellido" name="apellido"
                                           class="form-control @error('apellido') is-invalid @enderror"
                                           value="{{ old('apellido') }}" required autocomplete="family-name">
                                    @error('apellido')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="ci" class="form-label">CI <span class="form-hint-required">*</span></label>
                                    <input type="text" id="ci" name="ci"
                                           class="form-control @error('ci') is-invalid @enderror"
                                           value="{{ old('ci') }}" required>
                                    @error('ci')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="text" id="telefono" name="telefono"
                                           class="form-control @error('telefono') is-invalid @enderror"
                                           value="{{ old('telefono') }}" autocomplete="tel">
                                    @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="form-section">
                            <legend class="form-section-title">Acceso</legend>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="email" class="form-label">Correo electrónico <span class="form-hint-required">*</span></label>
                                    <input type="email" id="email" name="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email') }}" required autocomplete="email">
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Contraseña <span class="form-hint-required">*</span></label>
                                    <input type="password" id="password" name="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           required autocomplete="new-password">
                                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label">Confirmar contraseña <span class="form-hint-required">*</span></label>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                           class="form-control" required autocomplete="new-password">
                                </div>
                            </div>
                        </fieldset>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-brand">
                                <i class="bi bi-check-lg me-1" aria-hidden="true"></i>Guardar usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
