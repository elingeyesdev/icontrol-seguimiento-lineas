<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('template_title', config('app.name', 'Laravel'))</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- Bootstrap Icons -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --primary-color: #1A2D4F;
                --accent-color: #E07B15;
            }
            body {
                background-color: #f8f9fa;
                overflow-x: hidden;
            }
            .sidebar {
                background-color: var(--primary-color);
                border-right: 3px solid var(--accent-color);
                min-width: 260px;
                max-width: 260px;
                min-height: 100vh;
                transition: all 0.3s ease;
                z-index: 1035;
            }
            .sidebar .nav-link {
                color: rgba(255, 255, 255, 0.75);
                font-weight: 500;
                padding: 0.75rem 1.25rem;
                border-radius: 6px;
                margin: 0.2rem 0.8rem;
                display: flex;
                align-items: center;
                transition: all 0.2s ease;
                text-decoration: none;
            }
            .sidebar .nav-link:hover {
                color: #ffffff;
                background-color: rgba(255, 255, 255, 0.1);
                transform: translateX(3px);
            }
            .sidebar .nav-link.active {
                color: #ffffff;
                background-color: var(--accent-color);
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
            }
            .sidebar .menu-header {
                color: rgba(255, 255, 255, 0.4);
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 1px;
                font-weight: 700;
                padding: 1.25rem 1.25rem 0.5rem 1.25rem;
            }
            .text-warning-custom {
                color: var(--accent-color);
            }
            .main-content {
                min-height: 100vh;
            }
            
            /* Responsive Sidebar Overlay */
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1030;
                backdrop-filter: blur(2px);
            }
            
            @media (max-width: 767.98px) {
                .sidebar {
                    position: fixed;
                    left: -260px;
                    height: 100%;
                }
                .sidebar.show {
                    left: 0;
                }
                .sidebar-overlay.show {
                    display: block;
                }
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="d-flex min-vh-100">
            <!-- Overlay for mobile sidebar -->
            <div id="sidebarOverlay" class="sidebar-overlay" onclick="toggleSidebar()"></div>

            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Main Panel -->
            <div class="flex-grow-1 d-flex flex-column min-w-0 main-content">
                <!-- Top Nav -->
                @include('layouts.navigation')

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white border-bottom shadow-sm">
                        <div class="container-fluid py-3 px-4">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="flex-grow-1 p-3 p-md-4 bg-light">
                    {{ $slot ?? '' }}
                    @yield('content')
                </main>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        
        <script>
            function toggleSidebar() {
                document.getElementById('sidebar').classList.toggle('show');
                document.getElementById('sidebarOverlay').classList.toggle('show');
            }
        </script>
    </body>
</html>
