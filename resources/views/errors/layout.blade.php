<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex">

        <title>@yield('title', 'Error') · Línea 61</title>

        @vite('resources/css/errors.css')
    </head>
    <body>
        <main class="error-shell" id="contenido-principal">
            <div class="error-brand">
                <span class="error-brand-mark" aria-hidden="true">61</span>
                <span class="error-brand-text">
                    <span class="error-brand-name">Línea 61</span>
                    <span class="error-brand-tagline">Control y monitoreo de flota</span>
                </span>
            </div>

            <div class="error-card">
                @hasSection('code')
                    <p class="error-code">Error @yield('code')</p>
                @endif

                <h1 class="error-title">@yield('title', 'Ocurrió un error')</h1>

                <p class="error-message">
                    @yield('message', 'No fue posible completar la solicitud. Inténtalo nuevamente en unos instantes.')
                </p>

                <div class="error-actions">
                    <a class="error-btn error-btn--primary" href="{{ url('/') }}">Ir al inicio</a>
                    <a class="error-btn error-btn--secondary" href="{{ url('/login') }}">Iniciar sesión</a>
                </div>
            </div>

            <p class="error-footer">
                Si el problema continúa, comunícate con el administrador del sistema.
            </p>
        </main>
    </body>
</html>
