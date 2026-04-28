@extends('layouts.app')

@section('header')
    <h2 class="h4 text-dark mb-0">
        {{ __('Dashboard') }}
    </h2>
@endsection

@section('content')
<div class="container py-5">
    <div class="card shadow-sm mb-5 border-0">
        <div class="card-body bg-white rounded">
            <p class="mb-0 text-muted">
                {{ __("¡Bienvenido al sistema de control y seguimiento de líneas!") }}
            </p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Conductores -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0 border-top border-primary border-4">
                <div class="card-body">
                    <h5 class="card-title text-dark">Conductores</h5>
                    <p class="card-text text-muted">Gestiona los conductores del sistema.</p>
                    <a href="{{ route('conductor.index') }}" class="btn btn-primary btn-sm text-uppercase fw-bold">
                        Ver Conductores
                    </a>
                </div>
            </div>
        </div>

        <!-- Dueños -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0 border-top border-success border-4">
                <div class="card-body">
                    <h5 class="card-title text-dark">Dueños</h5>
                    <p class="card-text text-muted">Administra los dueños de los micros.</p>
                    <a href="{{ route('dueño.index') }}" class="btn btn-success btn-sm text-uppercase fw-bold">
                        Ver Dueños
                    </a>
                </div>
            </div>
        </div>

        <!-- Micros -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0 border-top border-info border-4">
                <div class="card-body">
                    <h5 class="card-title text-dark">Micros</h5>
                    <p class="card-text text-muted">Controla la flota de micros.</p>
                    <a href="{{ route('micro.index') }}" class="btn btn-info btn-sm text-uppercase fw-bold text-white">
                        Ver Micros
                    </a>
                </div>
            </div>
        </div>

        <!-- Internos -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0 border-top border-warning border-4">
                <div class="card-body">
                    <h5 class="card-title text-dark">Internos</h5>
                    <p class="card-text text-muted">Gestiona los internos del sistema.</p>
                    <a href="{{ route('interno.index') }}" class="btn btn-warning btn-sm text-uppercase fw-bold text-white">
                        Ver Internos
                    </a>
                </div>
            </div>
        </div>

        <!-- Rutas -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0 border-top border-danger border-4">
                <div class="card-body">
                    <h5 class="card-title text-dark">Rutas</h5>
                    <p class="card-text text-muted">Administra las rutas de los micros.</p>
                    <a href="{{ route('ruta.index') }}" class="btn btn-danger btn-sm text-uppercase fw-bold">
                        Ver Rutas
                    </a>
                </div>
            </div>
        </div>

        <!-- Turnos -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0 border-top border-secondary border-4">
                <div class="card-body">
                    <h5 class="card-title text-dark">Turnos</h5>
                    <p class="card-text text-muted">Controla los turnos de trabajo.</p>
                    <a href="{{ route('turno.index') }}" class="btn btn-secondary btn-sm text-uppercase fw-bold">
                        Ver Turnos
                    </a>
                </div>
            </div>
        </div>

        <!-- Roles -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0 border-top border-dark border-4">
                <div class="card-body">
                    <h5 class="card-title text-dark">Roles</h5>
                    <p class="card-text text-muted">Gestiona los roles y permisos.</p>
                    <a href="{{ route('roles.index') }}" class="btn btn-dark btn-sm text-uppercase fw-bold">
                        Ver Roles
                    </a>
                </div>
            </div>
        </div>
        <!-- Usuarios -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0 border-top border-primary border-4" style="border-top-color: #1A2D4F !important;">
                <div class="card-body">
                    <h5 class="card-title text-dark">Usuarios</h5>
                    <p class="card-text text-muted">Gestiona los usuarios y asígnales roles.</p>
                    <a href="{{ route('users.index') }}" class="btn btn-primary btn-sm text-uppercase fw-bold text-white">
                        Ver Usuarios
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
