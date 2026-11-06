<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Línea 61') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="app-shell">
            @include('layouts.navigation')

            <div class="sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>

            <div class="content-area">
                <header class="topbar">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <button id="sidebarToggle" class="sidebar-toggle" type="button"
                                    aria-label="Abrir menú de navegación" aria-controls="sidebar"
                                    aria-expanded="false">
                                <i class="bi bi-list fs-5" aria-hidden="true"></i>
                            </button>
                            <div>
                                <div class="topbar-brand-title">Sistema de Control y Seguimiento de Micros</div>
                                <div class="topbar-brand-subtitle">
                                    <span class="badge badge-soon me-1">Línea 61</span>
                                    Santa Cruz de la Sierra · Bolivia
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <div class="topbar-user d-flex align-items-center gap-2">
                                <span class="avatar" aria-hidden="true">
                                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                </span>
                                <span class="pe-1">
                                    <span class="topbar-user-name d-block">{{ Auth::user()->name ?? 'Usuario' }}</span>
                                    <span class="topbar-user-role d-block">{{ Auth::user()->roles->first()?->name ?? 'Sin rol' }}</span>
                                </span>
                            </div>
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="logout-btn btn d-flex align-items-center gap-1">
                                    <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                                    <span>Cerrar sesión</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </header>

                <main class="p-3 p-lg-4">
                    @if (session('error'))
                        <div class="alert alert-soft-danger d-flex align-items-start gap-2" role="alert">
                            <i class="bi bi-exclamation-triangle-fill mt-1" aria-hidden="true"></i>
                            <div>{{ session('error') }}</div>
                        </div>
                    @endif

                    @yield('content')
                    {{ $slot ?? '' }}
                </main>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
