@extends('layouts.app')

@section('content')
@vite('resources/css/dashboard.css')

<div class="container-fluid py-4">

    <div class="dashboard-welcome mb-4">
        <div>
            <p class="dashboard-welcome__eyebrow mb-1">Panel de control</p>
            <h1 class="dashboard-welcome__title mb-1">Bienvenido, {{ $usuario->name }}</h1>
            <p class="dashboard-welcome__role mb-0">Rol: {{ $usuario->roles->first()?->name ?? 'Sin rol asignado' }}</p>
        </div>
    </div>

    <div class="dashboard-modules mb-4">
        @foreach($tarjetas as $modulo => $items)
            @if(count($items))
                <section class="dashboard-module">
                    <div class="dashboard-module__header">
                        <h2 class="section-title mb-0">{{ $modulo }}</h2>
                        <span class="small text-muted">{{ count($items) }} opciones</span>
                    </div>
                    <div class="row g-3">
                        @foreach($items as $tarjeta)
                            <div class="col-12 col-sm-6 col-xl-3">
                                <a class="dashboard-module-card" href="{{ route($tarjeta['ruta'], $tarjeta['parametros'] ?? []) }}">
                                    <span class="dashboard-module-card__icon" aria-hidden="true"><i class="bi {{ $tarjeta['icono'] }}"></i></span>
                                    <span class="dashboard-module-card__content">
                                        <span class="dashboard-module-card__title">{{ $tarjeta['titulo'] }}</span>
                                        <span class="dashboard-module-card__description">{{ $tarjeta['descripcion'] }}</span>
                                    </span>
                                    <i class="bi bi-arrow-up-right dashboard-module-card__arrow" aria-hidden="true"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        @endforeach
    </div>

</div>
@endsection
