<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Línea 57 - Sistema de Control</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
        body {
            background-color: #f8f9fa;
        }
        .hero {
            background-color: #1A2D4F; /* Azul Navy */
            color: white;
            padding: 100px 0;
            text-align: center;
            border-bottom: 5px solid #E07B15; /* Detalle anaranjado */
        }
        .hero h1 {
            font-size: 3rem;
            font-weight: bold;
        }
        .hero p {
            font-size: 1.25rem;
            margin-bottom: 30px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #6c757d;
            margin-top: 50px;
        }
        /* Override Bootstrap Primary */
        .btn-primary {
            background-color: #E07B15 !important;
            border-color: #E07B15 !important;
            color: white !important;
        }
        .btn-primary:hover {
            background-color: #c76b12 !important;
            border-color: #c76b12 !important;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #1A2D4F;">
        <div class="container">
            <a class="navbar-brand" href="#">Línea 57</a>
            <div class="ms-auto">
                @if (Route::has('login'))
                    <div class="d-flex gap-2">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-outline-light">Ir al Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-light">Ingresar</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-primary">Registrarse</a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </nav>

    <div class="hero">
        <div class="container">
            <h1>Bienvenido al Sistema de Control</h1>
            <p>Plataforma para la gestión, seguimiento y control de los recorridos de la Línea 57.</p>
            @guest
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Comenzar ahora</a>
            @endguest
        </div>
    </div>

    <div class="container mt-5">
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <h4 class="card-title text-primary">Micros y Rutas</h4>
                        <p class="card-text text-muted">Lleva un registro completo de toda la flota y los recorridos asignados.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <h4 class="card-title text-success">Personal</h4>
                        <p class="card-text text-muted">Gestión de conductores, dueños de micros y administración de roles.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <h4 class="card-title text-warning">Turnos</h4>
                        <p class="card-text text-muted">Organización de horarios, turnos e internos de forma rápida y sencilla.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="container">
            <p>&copy; {{ date('Y') }} Línea 57. Proyecto universitario.</p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>