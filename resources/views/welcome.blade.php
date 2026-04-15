
Welcome linea57.blade · PHP
Copiar

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Línea 57 — {{ config('app.name', 'Laravel') }}</title>
 
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:ital,wght@0,400;0,600;0,700;1,600&family=Barlow:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
 
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
 
        <style>
            :root {
                --amber:      #E07B15;
                --amber-lt:   #F4A23A;
                --amber-bg:   #FEF3E2;
                --navy:       #1A2D4F;
                --navy-lt:    #243C6A;
                --navy-bg:    #EBF0F8;
                --cream:      #F9F7F4;
                --ink:        #131A27;
                --ink-soft:   #5B6472;
                --rule:       rgba(19,26,39,.10);
                --white:      #FFFFFF;
            }
 
            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
            html { font-size: 16px; }
 
            body {
                font-family: 'Barlow', sans-serif;
                background: var(--cream);
                color: var(--ink);
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                overflow-x: hidden;
            }
 
            /* horizontal rule stripe at top */
            body::before {
                content: '';
                position: fixed;
                top: 0; left: 0; right: 0;
                height: 4px;
                background: repeating-linear-gradient(
                    90deg,
                    var(--amber) 0px,
                    var(--amber) 28px,
                    var(--navy) 28px,
                    var(--navy) 56px
                );
                z-index: 100;
            }
 
            .page {
                position: relative;
                z-index: 1;
                display: flex;
                flex-direction: column;
                min-height: 100vh;
                padding: 0 1.5rem;
            }
 
            /* ── header / nav ── */
            header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 1.4rem 0 1rem;
                border-bottom: 1px solid var(--rule);
            }
 
            .brand {
                display: flex;
                align-items: center;
                gap: .75rem;
            }
 
            .line-badge {
                font-family: 'Barlow Condensed', sans-serif;
                font-weight: 700;
                font-size: 1.5rem;
                color: var(--white);
                background: var(--amber);
                width: 48px; height: 48px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                letter-spacing: -.02em;
                flex-shrink: 0;
            }
 
            .brand-text {
                display: flex;
                flex-direction: column;
            }
 
            .brand-name {
                font-family: 'Barlow Condensed', sans-serif;
                font-weight: 600;
                font-size: 1rem;
                letter-spacing: .04em;
                text-transform: uppercase;
                color: var(--navy);
                line-height: 1;
            }
 
            .brand-sub {
                font-family: 'DM Mono', monospace;
                font-size: .65rem;
                color: var(--ink-soft);
                letter-spacing: .06em;
                margin-top: 2px;
            }
 
            .nav-links { display: flex; gap: .5rem; }
 
            .nav-link {
                font-family: 'DM Mono', monospace;
                font-size: .7rem;
                letter-spacing: .08em;
                text-transform: uppercase;
                text-decoration: none;
                padding: .4rem .9rem;
                border-radius: 4px;
                border: 1px solid var(--rule);
                color: var(--ink-soft);
                transition: all .18s ease;
                background: transparent;
            }
            .nav-link:hover { border-color: var(--amber); color: var(--amber); }
            .nav-link.solid {
                background: var(--navy);
                border-color: var(--navy);
                color: var(--white);
            }
            .nav-link.solid:hover {
                background: var(--navy-lt);
                border-color: var(--navy-lt);
            }
 
            /* ── main ── */
            main {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 3rem 0 4rem;
            }
 
            .shell {
                width: 100%;
                max-width: 920px;
                display: grid;
                grid-template-columns: 1fr;
                border-radius: 12px;
                overflow: hidden;
                border: 1px solid var(--rule);
                box-shadow: 0 4px 24px rgba(19,26,39,.07);
                animation: up .5s cubic-bezier(.22,1,.36,1) both;
            }
 
            @keyframes up {
                from { opacity: 0; transform: translateY(18px); }
                to   { opacity: 1; transform: translateY(0); }
            }
 
            @media (min-width: 700px) {
                .shell { grid-template-columns: 1fr 380px; }
            }
 
            /* ── left panel ── */
            .panel-left {
                background: var(--white);
                padding: 2.5rem 2.5rem 2rem;
                display: flex;
                flex-direction: column;
                gap: 1.75rem;
            }
 
            .kicker {
                font-family: 'DM Mono', monospace;
                font-size: .68rem;
                letter-spacing: .12em;
                text-transform: uppercase;
                color: var(--amber);
                display: flex;
                align-items: center;
                gap: .5rem;
            }
            .kicker::before {
                content: '';
                width: 18px; height: 2px;
                background: var(--amber);
                display: block;
            }
 
            .headline {
                font-family: 'Barlow Condensed', sans-serif;
                font-weight: 700;
                font-size: clamp(2rem, 5vw, 2.8rem);
                line-height: 1.05;
                letter-spacing: -.01em;
                color: var(--navy);
            }
            .headline span {
                color: var(--amber);
                font-style: italic;
            }
 
            .body-text {
                font-size: .9rem;
                line-height: 1.7;
                color: var(--ink-soft);
                font-weight: 300;
                max-width: 40ch;
            }
 
            /* ── steps ── */
            .steps { list-style: none; }
 
            .step {
                display: grid;
                grid-template-columns: 20px 1fr;
                gap: .75rem;
                align-items: start;
                padding: .8rem 0;
                border-bottom: 1px dashed var(--rule);
            }
            .step:last-child { border-bottom: none; }
 
            .step-n {
                font-family: 'DM Mono', monospace;
                font-size: .6rem;
                color: var(--amber);
                letter-spacing: .06em;
                padding-top: 3px;
            }
 
            .step-txt {
                font-size: .875rem;
                color: var(--ink-soft);
                line-height: 1.5;
            }
 
            .link {
                color: var(--navy);
                font-weight: 500;
                text-decoration: none;
                border-bottom: 1.5px solid rgba(26,45,79,.25);
                padding-bottom: 1px;
                display: inline-flex;
                align-items: center;
                gap: .25rem;
                transition: all .15s;
            }
            .link:hover { color: var(--amber); border-color: var(--amber); }
 
            /* ── CTA ── */
            .cta-row { display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; }
 
            .btn {
                font-family: 'Barlow Condensed', sans-serif;
                font-size: .95rem;
                font-weight: 600;
                letter-spacing: .06em;
                text-transform: uppercase;
                text-decoration: none;
                padding: .6rem 1.5rem;
                border-radius: 5px;
                display: inline-flex;
                align-items: center;
                gap: .5rem;
                transition: all .18s ease;
                border: 2px solid transparent;
            }
 
            .btn-primary {
                background: var(--amber);
                color: var(--white);
                border-color: var(--amber);
            }
            .btn-primary:hover {
                background: var(--amber-lt);
                border-color: var(--amber-lt);
                transform: translateY(-1px);
            }
 
            .version-tag {
                font-family: 'DM Mono', monospace;
                font-size: .65rem;
                color: var(--ink-soft);
                opacity: .55;
                margin-top: auto;
                display: flex;
                gap: .6rem;
                align-items: center;
            }
            .version-tag a { color: var(--navy); opacity: 1; text-decoration: none; }
            .version-tag a:hover { text-decoration: underline; }
 
            /* ── right panel ── */
            .panel-right {
                background: var(--navy);
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 2.5rem 2rem;
                position: relative;
                overflow: hidden;
                min-height: 280px;
                gap: 1.5rem;
            }
 
            /* diagonal lines texture */
            .panel-right::before {
                content: '';
                position: absolute;
                inset: 0;
                background-image: repeating-linear-gradient(
                    -45deg,
                    rgba(255,255,255,.025) 0px,
                    rgba(255,255,255,.025) 1px,
                    transparent 1px,
                    transparent 14px
                );
            }
 
            /* amber bottom accent */
            .panel-right::after {
                content: '';
                position: absolute;
                bottom: 0; left: 0; right: 0;
                height: 3px;
                background: linear-gradient(90deg, var(--amber) 0%, var(--amber-lt) 100%);
            }
 
            /* the bus illustration */
            .bus-wrap {
                position: relative;
                z-index: 1;
                width: 100%;
                max-width: 300px;
                animation: up .7s .1s cubic-bezier(.22,1,.36,1) both;
            }
 
            .route-chip {
                position: relative;
                z-index: 1;
                font-family: 'Barlow Condensed', sans-serif;
                font-weight: 600;
                font-size: .8rem;
                letter-spacing: .1em;
                text-transform: uppercase;
                color: rgba(255,255,255,.55);
                border: 1px solid rgba(255,255,255,.12);
                padding: .35rem 1rem;
                border-radius: 100px;
                background: rgba(255,255,255,.05);
            }
 
            .stops-list {
                position: relative;
                z-index: 1;
                display: flex;
                flex-direction: column;
                gap: .4rem;
                width: 100%;
                max-width: 260px;
            }
 
            .stop-row {
                display: flex;
                align-items: center;
                gap: .6rem;
                font-size: .75rem;
                color: rgba(255,255,255,.55);
                font-family: 'DM Mono', monospace;
            }
 
            .stop-dot {
                width: 6px; height: 6px;
                border-radius: 50%;
                background: var(--amber);
                flex-shrink: 0;
            }
 
            .stop-dot.dim { background: rgba(255,255,255,.25); }
 
            .stop-line {
                width: 1px; height: 12px;
                background: rgba(255,255,255,.15);
                margin-left: 2.5px;
            }
 
            /* dark mode */
            @media (prefers-color-scheme: dark) {
                :root {
                    --cream:   #0C1117;
                    --white:   #141B25;
                    --ink:     #DDE3EC;
                    --ink-soft:#7A8594;
                    --rule:    rgba(255,255,255,.08);
                    --amber-bg: rgba(224,123,21,.1);
                    --navy-bg:  rgba(26,45,79,.2);
                }
                .panel-right { background: #0F1D33; }
            }
 
            @media (max-width: 699px) {
                .panel-right { order: -1; }
                .panel-left  { padding: 2rem 1.5rem; }
                header { flex-direction: column; align-items: flex-start; gap: .75rem; }
            }
        </style>
    </head>
    <body>
        <div class="page">
            <header>
                <div class="brand">
                    <div class="line-badge">57</div>
                    <div class="brand-text">
                        <span class="brand-name">Línea 57</span>
                        <span class="brand-sub">Servicio urbano de transporte</span>
                    </div>
                </div>
 
                @if (Route::has('login'))
                    <nav class="nav-links">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="nav-link solid">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="nav-link">Ingresar</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="nav-link solid">Registrarse</a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </header>
 
            <main>
                <div class="shell">
                    <!-- izquierda -->
                    <div class="panel-left">
                        <p class="kicker">Plataforma de gestión</p>
 
                        <h1 class="headline">
                            Tu recorrido,<br>
                            <span>siempre en hora.</span>
                        </h1>
 
                        <p class="body-text">
                            Sistema de administración para la línea 57. Consultá horarios, gestioná paradas y coordiná el servicio desde un solo lugar.
                        </p>
 
                        <ul class="steps">
                            <li class="step">
                                <span class="step-n">01</span>
                                <p class="step-txt">
                                    Revisá la
                                    <a href="https://laravel.com/docs" target="_blank" class="link">
                                        documentación técnica
                                        <svg width="9" height="9" viewBox="0 0 10 10" fill="none"><path d="M7.5 6.5V2.5H3.5M2 8L7.5 2.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                                    </a>
                                    para configurar el sistema.
                                </p>
                            </li>
                            <li class="step">
                                <span class="step-n">02</span>
                                <p class="step-txt">
                                    Seguí los tutoriales en
                                    <a href="https://laracasts.com" target="_blank" class="link">
                                        Laracasts
                                        <svg width="9" height="9" viewBox="0 0 10 10" fill="none"><path d="M7.5 6.5V2.5H3.5M2 8L7.5 2.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                                    </a>
                                    si necesitás soporte extra.
                                </p>
                            </li>
                        </ul>
 
                        <div class="cta-row">
                            <a href="https://cloud.laravel.com" target="_blank" class="btn btn-primary">
                                <svg width="13" height="13" viewBox="0 0 14 14" fill="none"><path d="M7 1v8M4 6l3 3 3-3M2 11h10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Desplegar sistema
                            </a>
                        </div>
 
                        <p class="version-tag">
                            Laravel v{{ app()->version() }} &nbsp;·&nbsp;
                            <a href="https://github.com/laravel/laravel/blob/13.x/CHANGELOG.md" target="_blank">Ver changelog ↗</a>
                        </p>
                    </div>
 
                    <!-- derecha: ilustración micro + paradas -->
                    <div class="panel-right">
                        <!-- SVG bus -->
                        <div class="bus-wrap">
                            <svg viewBox="0 0 300 130" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <!-- carrocería -->
                                <rect x="8" y="22" width="270" height="82" rx="10" fill="#1E3558"/>
                                <!-- franja naranja lateral -->
                                <rect x="8" y="52" width="270" height="16" fill="#E07B15"/>
                                <!-- techo -->
                                <rect x="16" y="14" width="255" height="14" rx="5" fill="#162C4A"/>
                                <!-- parabrisas delantero -->
                                <rect x="248" y="28" width="24" height="34" rx="4" fill="#2E5A8A" opacity=".9"/>
                                <!-- ventanas laterales -->
                                <rect x="24"  y="28" width="36" height="20" rx="3" fill="#2E5A8A" opacity=".85"/>
                                <rect x="68"  y="28" width="36" height="20" rx="3" fill="#2E5A8A" opacity=".85"/>
                                <rect x="112" y="28" width="36" height="20" rx="3" fill="#2E5A8A" opacity=".85"/>
                                <rect x="156" y="28" width="36" height="20" rx="3" fill="#2E5A8A" opacity=".85"/>
                                <rect x="200" y="28" width="36" height="20" rx="3" fill="#2E5A8A" opacity=".85"/>
                                <!-- destino chip -->
                                <rect x="24" y="33" width="92" height="10" rx="2" fill="#E07B15" opacity=".2"/>
                                <text x="70" y="41" text-anchor="middle" font-family="'Barlow Condensed', sans-serif" font-size="8" font-weight="700" fill="#F4A23A" letter-spacing="1">CENTRO — TERMINAL</text>
                                <!-- número de línea en frente -->
                                <rect x="252" y="32" width="18" height="14" rx="2" fill="#E07B15"/>
                                <text x="261" y="43" text-anchor="middle" font-family="'Barlow Condensed', sans-serif" font-size="9" font-weight="700" fill="white">57</text>
                                <!-- puerta -->
                                <rect x="196" y="62" width="18" height="38" rx="2" fill="#132338" opacity=".6"/>
                                <line x1="205" y1="62" x2="205" y2="100" stroke="#E07B15" stroke-width=".8" opacity=".4"/>
                                <!-- ruedas -->
                                <circle cx="52"  cy="108" r="16" fill="#0C1D33"/>
                                <circle cx="52"  cy="108" r="9"  fill="#162C4A"/>
                                <circle cx="52"  cy="108" r="4"  fill="#E07B15" opacity=".6"/>
                                <circle cx="234" cy="108" r="16" fill="#0C1D33"/>
                                <circle cx="234" cy="108" r="9"  fill="#162C4A"/>
                                <circle cx="234" cy="108" r="4"  fill="#E07B15" opacity=".6"/>
                                <!-- suelo / sombra -->
                                <ellipse cx="143" cy="124" rx="130" ry="5" fill="#0C1D33" opacity=".25"/>
                                <!-- luz delantera -->
                                <rect x="270" y="60" width="10" height="8" rx="2" fill="#F4A23A" opacity=".8"/>
                                <!-- luz trasera -->
                                <rect x="8" y="60" width="8" height="8" rx="2" fill="#E07B15" opacity=".7"/>
                            </svg>
                        </div>
 
                        <span class="route-chip">Recorrido activo</span>
 
                        <div class="stops-list">
                            <div class="stop-row"><div class="stop-dot"></div><span>Terminal de Ómnibus</span></div>
                            <div class="stop-row"><div class="stop-line"></div></div>
                            <div class="stop-row"><div class="stop-dot dim"></div><span>Av. Cañoto y Suárez</span></div>
                            <div class="stop-row"><div class="stop-line"></div></div>
                            <div class="stop-row"><div class="stop-dot dim"></div><span>Plaza Principal</span></div>
                            <div class="stop-row"><div class="stop-line"></div></div>
                            <div class="stop-row"><div class="stop-dot dim"></div><span>Mercado Los Pozos</span></div>
                            <div class="stop-row"><div class="stop-line"></div></div>
                            <div class="stop-row"><div class="stop-dot"></div><span>Centro Comercial</span></div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>