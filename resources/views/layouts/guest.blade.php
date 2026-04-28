<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

        <style>
            body {
                /* Color Azul Navy */
                background-color: #1A2D4F; 
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                margin: 0;
            }
            .auth-container {
                width: 100%;
                max-width: 400px;
                padding: 15px;
            }
            .auth-card {
                background-color: #ffffff;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                padding: 30px;
                border-top: 5px solid #E07B15; /* Detalle anaranjado */
            }
            .auth-logo {
                text-align: center;
                margin-bottom: 20px;
            }
            .auth-logo h1 {
                color: #ffffff;
                font-weight: 600;
                font-size: 24px;
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
        <div class="auth-container">
            <div class="auth-logo">
                <!-- Se puede cambiar por un logo real -->
                <a href="/" style="text-decoration: none;">
                    <h1>{{ config('app.name', 'Sistema') }}</h1>
                </a>
            </div>
            <div class="auth-card">
                {{ $slot }}
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
