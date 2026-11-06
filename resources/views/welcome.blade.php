<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Línea 61 | Sistema de Control & Monitoreo</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            --navy-dark: #071E3D;
            --navy-card: #182C4D;
            --navy-blue: #0B3C78;
            --accent-guindo: #7B1E2B;
            --accent-guindo-hover: #9E2235;
            --surface-light: #F8FAFC;
            --text-dark: #0F172A;
            --text-muted: #64748B;
            --border-light: #E2E8F0;
        }

        * {
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
        }

        body {
            background-color: var(--surface-light);
            color: var(--text-dark);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* Top Navbar */
        .landing-navbar {
            background: #071E3D;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            padding: 0.85rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .brand-logo-badge {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(123,30,43,0.95) 100%);
            border: 1.5px solid rgba(255,255,255,0.35);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
        }

        /* Botones de Alta Visibilidad para Navbar */
        .btn-nav-login {
            background-color: #FFFFFF !important;
            color: #071E3D !important;
            border: 1.5px solid #FFFFFF !important;
            border-radius: 9px !important;
            font-weight: 700 !important;
            font-size: 0.9rem !important;
            padding: 0.55rem 1.25rem !important;
            transition: all 0.2s ease !important;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            text-decoration: none !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }
        .btn-nav-login:hover {
            background-color: #F1F5F9 !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .btn-nav-register {
            background: linear-gradient(135deg, #7B1E2B 0%, #9E2235 100%) !important;
            color: #FFFFFF !important;
            border: 1.5px solid #9E2235 !important;
            border-radius: 9px !important;
            font-weight: 700 !important;
            font-size: 0.9rem !important;
            padding: 0.55rem 1.25rem !important;
            transition: all 0.2s ease !important;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            text-decoration: none !important;
            box-shadow: 0 2px 10px rgba(123, 30, 43, 0.4);
        }
        .btn-nav-register:hover {
            background: linear-gradient(135deg, #9E2235 0%, #B91C1C 100%) !important;
            border-color: #B91C1C !important;
            color: #FFFFFF !important;
            transform: translateY(-1px);
        }

        /* Hero Section con GIF traffvehicular.gif */
        .hero-section {
            position: relative;
            background: #071E3D url('{{ asset('images/traffvehicular.gif') }}') center center / cover no-repeat;
            min-height: 540px;
            display: flex;
            align-items: center;
            padding: 4.5rem 0 5.5rem 0;
            overflow: hidden;
            border-bottom: 5px solid var(--accent-guindo);
        }
        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(7, 30, 61, 0.92) 0%, rgba(11, 60, 120, 0.85) 55%, rgba(123, 30, 43, 0.88) 100%);
            z-index: 1;
        }
        .hero-content {
            position: relative;
            z-index: 2;
        }

        /* Botones de Acción del Hero */
        .btn-hero-primary {
            background: linear-gradient(135deg, #7B1E2B 0%, #9E2235 100%) !important;
            color: #FFFFFF !important;
            border: 2px solid #9E2235 !important;
            border-radius: 12px !important;
            font-weight: 700 !important;
            font-size: 1.05rem !important;
            padding: 0.85rem 1.85rem !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 6px 20px rgba(123, 30, 43, 0.5) !important;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none !important;
        }
        .btn-hero-primary:hover {
            background: linear-gradient(135deg, #9E2235 0%, #B91C1C 100%) !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(123, 30, 43, 0.65) !important;
        }

        .btn-hero-secondary {
            background-color: #FFFFFF !important;
            color: #071E3D !important;
            border: 2px solid #FFFFFF !important;
            border-radius: 12px !important;
            font-weight: 700 !important;
            font-size: 1.05rem !important;
            padding: 0.85rem 1.85rem !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25) !important;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none !important;
        }
        .btn-hero-secondary:hover {
            background-color: #F1F5F9 !important;
            color: #071E3D !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.35) !important;
        }

        /* Feature Cards */
        .feature-card-custom {
            background: #FFFFFF;
            border: 1.5px solid var(--border-light);
            border-radius: 16px;
            padding: 2.2rem 1.8rem;
            transition: all 0.25s ease;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }
        .feature-card-custom::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--navy-blue) 0%, var(--accent-guindo) 100%);
            opacity: 0;
            transition: opacity 0.25s ease;
        }
        .feature-card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 36px rgba(11, 60, 120, 0.12);
            border-color: #CBD5E1;
        }
        .feature-card-custom:hover::before {
            opacity: 1;
        }

        .feature-icon-box {
            width: 54px;
            height: 54px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.25rem;
        }
        .icon-box-blue {
            background: rgba(11, 60, 120, 0.12);
            color: var(--navy-blue);
        }
        .icon-box-guindo {
            background: rgba(123, 30, 43, 0.12);
            color: var(--accent-guindo);
        }

        /* Footer */
        .landing-footer {
            background: var(--navy-dark);
            color: rgba(255, 255, 255, 0.75);
            padding: 2.5rem 0;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body>
    <!-- Top Navbar -->
    <nav class="landing-navbar">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="{{ url('/') }}" class="d-flex align-items-center gap-3 text-decoration-none text-white">
                <div class="brand-logo-badge">
                    <i class="bi bi-bus-front fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-white" style="letter-spacing: -0.3px;">Línea 61</h5>
                    <span class="small text-white-50" style="font-size: 0.75rem;">Control Operativo de Micros</span>
                </div>
            </a>

            <div>
                @if (Route::has('login'))
                    <div class="d-flex align-items-center gap-2">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-nav-register">
                                <i class="bi bi-grid-1x2"></i>
                                <span>Panel de Control</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-nav-login">
                                <i class="bi bi-box-arrow-in-right"></i>
                                <span>Ingresar</span>
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-nav-register">
                                    <i class="bi bi-person-plus"></i>
                                    <span>Registrarse</span>
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section con fondo GIF de tráfico vehicular y overlay azul/guindo -->
    <section class="hero-section">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <div class="row align-items-center">
                <div class="col-lg-10">
                    <!-- Badge Superior -->
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3" 
                         style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); border: 1.5px solid rgba(255,255,255,0.35);">
                        <i class="bi bi-broadcast text-info"></i>
                        <span class="text-white small fw-bold tracking-wider text-uppercase" style="font-size: 0.78rem; letter-spacing: 0.08em;">
                            Sistema Centralizado · Santa Cruz de la Sierra
                        </span>
                    </div>

                    <!-- Título Principal -->
                    <h1 class="display-4 fw-bold text-white mb-3" style="line-height: 1.15; letter-spacing: -0.8px;">
                        Control y Monitoreo Inteligente de Microbuses en Tiempo Real
                    </h1>

                    <!-- Descripción -->
                    <p class="text-white text-opacity-90 fs-5 mb-4" style="max-width: 680px; font-weight: 400; line-height: 1.6;">
                        Plataforma integral para el seguimiento satelital de la flota, auditoría de paradas autorizadas, programación de turnos y control de recorridos de la <strong>Línea 61</strong>.
                    </p>

                    <!-- Botones de Acción Destacados -->
                    <div class="d-flex flex-wrap gap-3 pt-2">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-hero-primary">
                                <span>Ir al Panel de Control</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-hero-primary">
                                <span>Acceder al Sistema</span>
                                <i class="bi bi-arrow-right-circle"></i>
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-hero-secondary">
                                    <i class="bi bi-person-plus"></i>
                                    <span>Crear Cuenta</span>
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cards de Módulos del Proyecto -->
    <section class="container py-5 my-3">
        <div class="text-center max-w-700 mx-auto mb-5">
            <div class="d-inline-block badge px-3 py-1 rounded-2 mb-2" style="background: rgba(123, 30, 43, 0.1); color: var(--accent-guindo); font-weight: 700; font-size: 0.78rem;">
                MÓDULOS DE CONTROL
            </div>
            <h2 class="fw-bold text-dark display-6 mb-2" style="letter-spacing: -0.5px;">
                Gestión Operativa de la Línea 61
            </h2>
            <p class="text-muted fs-6 mb-0">
                Herramientas diseñadas para la supervisión y despacho eficiente de microbuses en Santa Cruz.
            </p>
        </div>

        <div class="row g-4">
            <!-- Card 1: Monitoreo GPS -->
            <div class="col-md-6 col-lg-4">
                <article class="feature-card-custom">
                    <div class="feature-icon-box icon-box-blue">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-2" style="font-size: 1.15rem;">Monitoreo Satelital GPS</h4>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">
                        Seguimiento en mapa interactivo de las unidades en ruta, velocidad de desplazamiento, sentido de circulación y estado en tiempo real.
                    </p>
                </article>
            </div>

            <!-- Card 2: Control de Paradas -->
            <div class="col-md-6 col-lg-4">
                <article class="feature-card-custom">
                    <div class="feature-icon-box icon-box-guindo">
                        <i class="bi bi-pin-map-fill"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-2" style="font-size: 1.15rem;">Control de Paradas</h4>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">
                        Registro y supervisión del cumplimiento de paradas obligatorias, cálculo de tiempos de llegada y detección inmediata de demoras.
                    </p>
                </article>
            </div>

            <!-- Card 3: Asignación de Turnos -->
            <div class="col-md-6 col-lg-4">
                <article class="feature-card-custom">
                    <div class="feature-icon-box icon-box-blue">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-2" style="font-size: 1.15rem;">Asignación de Turnos</h4>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">
                        Programación organizada de jornadas por turnos (mañana, tarde y noche), asociando cada número interno con su chofer correspondiente.
                    </p>
                </article>
            </div>

            <!-- Card 4: Flota de Microbuses -->
            <div class="col-md-6 col-lg-4">
                <article class="feature-card-custom">
                    <div class="feature-icon-box icon-box-guindo">
                        <i class="bi bi-bus-front-fill"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-2" style="font-size: 1.15rem;">Control de Flota</h4>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">
                        Padrón digital de micros activos, números internos, placas, modelos, capacidad de pasajeros y vinculación con los propietarios.
                    </p>
                </article>
            </div>

            <!-- Card 5: Conductores y Personal -->
            <div class="col-md-6 col-lg-4">
                <article class="feature-card-custom">
                    <div class="feature-icon-box icon-box-blue">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-2" style="font-size: 1.15rem;">Conductores & Personal</h4>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">
                        Gestión de fichas de choferes, licencias de conducir, teléfonos de contacto, historial de conducción y estado de asignación.
                    </p>
                </article>
            </div>

            <!-- Card 6: Rutas y Reportes -->
            <div class="col-md-6 col-lg-4">
                <article class="feature-card-custom">
                    <div class="feature-icon-box icon-box-guindo">
                        <i class="bi bi-signpost-2-fill"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-2" style="font-size: 1.15rem;">Rutas y Auditoría</h4>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">
                        Definición de trazados de ida y vuelta, paradas intermedias, bitácora de recorridos y reportes estadísticos para administración.
                    </p>
                </article>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="landing-footer">
        <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 text-center text-md-start">
            <div class="d-flex align-items-center gap-2">
                <div class="brand-logo-badge" style="width: 36px; height: 36px;">
                    <i class="bi bi-bus-front fs-5"></i>
                </div>
                <div>
                    <span class="fw-bold text-white">Línea 61</span>
                    <span class="text-white-50 small ms-2">· Sistema de Control y Monitoreo</span>
                </div>
            </div>
            <div class="small text-white-50">
                &copy; {{ date('Y') }} Línea 61 · Santa Cruz de la Sierra, Bolivia. Todos los derechos reservados.
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>