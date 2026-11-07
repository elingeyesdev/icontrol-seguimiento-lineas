<aside class="sidebar" id="sidebar" aria-label="Navegación principal">
    <div class="brand-container d-flex align-items-center justify-content-between mb-3 pb-3 px-1">
        <a href="{{ route('dashboard') }}" class="text-decoration-none text-white d-flex align-items-center gap-2">
            <span class="brand-logo rounded-3 d-flex align-items-center justify-content-center">
                <i class="bi bi-bus-front fs-5" aria-hidden="true"></i>
            </span>
            <span class="brand-text">
                <span class="brand-title fw-bold text-white d-block">Línea 61</span>
                <span class="brand-subtitle d-block">Control de flota</span>
            </span>
        </a>
        <button id="sidebarClose" class="sidebar-close" type="button" aria-label="Cerrar menú de navegación">
            <i class="bi bi-x-lg" aria-hidden="true"></i>
        </button>
    </div>

    <div class="menu-title">General</div>
    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"
       @if(request()->routeIs('dashboard')) aria-current="page" @endif>
        <i class="bi bi-grid-1x2" aria-hidden="true"></i><span>Panel de Control</span>
    </a>

    <div class="menu-title">Módulos</div>

    {{-- MÓDULO 1: SEGURIDAD --}}
    @if(Auth::user()->hasRole(['admin', 'propietario', 'fiscalizador']))
    @php
        $seguridadActive = request()->routeIs('users.*') || request()->routeIs('roles.*');
    @endphp
    <a class="nav-link {{ $seguridadActive ? 'active' : '' }}"
       data-bs-toggle="collapse"
       href="#seguridadSubmenu"
       role="button"
       aria-expanded="{{ $seguridadActive ? 'true' : 'false' }}"
       aria-controls="seguridadSubmenu">
        <i class="bi bi-shield-lock" aria-hidden="true"></i>
        <span>Seguridad</span>
        <i class="bi bi-caret-down-fill" aria-hidden="true"></i>
    </a>
    <div class="collapse submenu {{ $seguridadActive ? 'show' : '' }}" id="seguridadSubmenu">
        <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}"
           @if(request()->routeIs('users.*')) aria-current="page" @endif>
            <i class="bi bi-people" aria-hidden="true"></i><span>Usuarios</span>
        </a>
        @if(Auth::user()->hasRole(['admin', 'fiscalizador']))
        <a class="nav-link {{ request()->routeIs('roles.*') && !request()->routeIs('roles.assign*') ? 'active' : '' }}" href="{{ route('roles.index') }}"
           @if(request()->routeIs('roles.*') && !request()->routeIs('roles.assign*')) aria-current="page" @endif>
            <i class="bi bi-shield" aria-hidden="true"></i><span>Roles</span>
        </a>
        <a class="nav-link {{ request()->routeIs('roles.assign*') ? 'active' : '' }}" href="{{ route('roles.assign') }}"
           @if(request()->routeIs('roles.assign*')) aria-current="page" @endif>
            <i class="bi bi-person-gear" aria-hidden="true"></i><span>Asignar Roles</span>
        </a>
        @endif
    </div>
    @endif

    {{-- MÓDULO 2: PARAMETRIZACIÓN --}}
    @if(Auth::user()->hasRole(['admin', 'propietario', 'fiscalizador']))
    @php
        $parametrizacionActive = request()->routeIs('conductor.*') ||
                                 request()->routeIs('propietario.*') ||
                                 request()->routeIs('micro.*') ||
                                 request()->routeIs('interno.*') ||
                                 request()->routeIs('ruta.*') ||
                                 request()->routeIs('parada.*') ||
                                 request()->routeIs('turno.*');
    @endphp
    <a class="nav-link {{ $parametrizacionActive ? 'active' : '' }}"
       data-bs-toggle="collapse"
       href="#parametrizacionSubmenu"
       role="button"
       aria-expanded="{{ $parametrizacionActive ? 'true' : 'false' }}"
       aria-controls="parametrizacionSubmenu">
        <i class="bi bi-sliders" aria-hidden="true"></i>
        <span>Parametrización</span>
        <i class="bi bi-caret-down-fill" aria-hidden="true"></i>
    </a>
    <div class="collapse submenu {{ $parametrizacionActive ? 'show' : '' }}" id="parametrizacionSubmenu">
        <a class="nav-link {{ request()->routeIs('propietario.*') ? 'active' : '' }}" href="{{ route('propietario.index') }}">
            <i class="bi bi-person-vcard" aria-hidden="true"></i><span>Propietarios</span>
        </a>
        <a class="nav-link {{ request()->routeIs('interno.*') ? 'active' : '' }}" href="{{ route('interno.index') }}">
            <i class="bi bi-hdd-stack" aria-hidden="true"></i><span>Internos</span>
        </a>
        <a class="nav-link {{ request()->routeIs('micro.*') ? 'active' : '' }}" href="{{ route('micro.index') }}">
            <i class="bi bi-bus-front" aria-hidden="true"></i><span>Micros</span>
        </a>
        <a class="nav-link {{ request()->routeIs('conductor.*') ? 'active' : '' }}" href="{{ route('conductor.index') }}">
            <i class="bi bi-person-badge" aria-hidden="true"></i><span>Conductores</span>
        </a>
        <a class="nav-link {{ request()->routeIs('ruta.*') ? 'active' : '' }}" href="{{ route('ruta.index') }}">
            <i class="bi bi-signpost-2" aria-hidden="true"></i><span>Rutas</span>
        </a>
        <a class="nav-link {{ request()->routeIs('parada.*') ? 'active' : '' }}" href="{{ route('parada.index') }}">
            <i class="bi bi-geo-alt" aria-hidden="true"></i><span>Paradas</span>
        </a>
        <a class="nav-link {{ request()->routeIs('turno.*') ? 'active' : '' }}" href="{{ route('turno.index') }}">
            <i class="bi bi-clock" aria-hidden="true"></i><span>Turnos</span>
        </a>
    </div>
    @endif

    {{-- MÓDULO 3: TRANSACCIONES --}}
    @if(Auth::user()->hasRole(['admin', 'propietario', 'fiscalizador']))
    @php
        $transaccionesActive = request()->routeIs('asignacion-turno.*') ||
                               request()->routeIs('seguimiento-rutas.*') ||
                               request()->routeIs('monitoreo.*') ||
                               request()->routeIs('control-recorrido.*');
    @endphp
    <a class="nav-link {{ $transaccionesActive ? 'active' : '' }}"
       data-bs-toggle="collapse"
       href="#transaccionesSubmenu"
       role="button"
       aria-expanded="{{ $transaccionesActive ? 'true' : 'false' }}"
       aria-controls="transaccionesSubmenu">
        <i class="bi bi-arrow-left-right" aria-hidden="true"></i>
        <span>Transacciones</span>
        <i class="bi bi-caret-down-fill" aria-hidden="true"></i>
    </a>
    <div class="collapse submenu {{ $transaccionesActive ? 'show' : '' }}" id="transaccionesSubmenu">
        <a class="nav-link {{ request()->routeIs('asignacion-turno.*') ? 'active' : '' }}" href="{{ route('asignacion-turno.index') }}">
            <i class="bi bi-calendar2-check" aria-hidden="true"></i><span>Asignación de Turnos</span>
        </a>
        <a class="nav-link {{ request()->routeIs('control-recorrido.*') ? 'active' : '' }}" href="{{ route('control-recorrido.index') }}">
            <i class="bi bi-signpost-split" aria-hidden="true"></i><span>Control de Recorrido</span>
        </a>
        <a class="nav-link {{ request()->routeIs('seguimiento-rutas.*') || request()->routeIs('monitoreo.*') ? 'active' : '' }}" href="{{ route('monitoreo.index') }}">
            <i class="bi bi-geo-alt" aria-hidden="true"></i><span>Seguimiento GPS</span>
        </a>
    </div>
    @endif

    {{-- MÓDULO 4: REPORTES --}}
    @if(Auth::user()->hasRole(['admin', 'propietario', 'fiscalizador']))
    @php
        $reportesActive = request()->routeIs('reportes.*');
        $reporteActivo = $reportesActive ? request()->route('reporte') : null;
    @endphp
    <a class="nav-link {{ $reportesActive ? 'active' : '' }}"
       data-bs-toggle="collapse"
       href="#reportesSubmenu"
       role="button"
       aria-expanded="{{ $reportesActive ? 'true' : 'false' }}"
       aria-controls="reportesSubmenu">
        <i class="bi bi-bar-chart-line" aria-hidden="true"></i>
        <span>Reportes</span>
        <i class="bi bi-caret-down-fill" aria-hidden="true"></i>
    </a>
    <div class="collapse submenu {{ $reportesActive ? 'show' : '' }}" id="reportesSubmenu">
        <a class="nav-link {{ $reporteActivo === 'asignaciones-conductor' ? 'active' : '' }}" href="{{ route('reportes.show', 'asignaciones-conductor') }}">
            <i class="bi bi-file-earmark-person" aria-hidden="true"></i><span>Asignaciones por Conductor</span>
        </a>
        <a class="nav-link {{ $reporteActivo === 'micros-propietario' ? 'active' : '' }}" href="{{ route('reportes.show', 'micros-propietario') }}">
            <i class="bi bi-file-earmark-spreadsheet" aria-hidden="true"></i><span>Micros por Propietario</span>
        </a>
        <a class="nav-link {{ $reporteActivo === 'paradas-omitidas' ? 'active' : '' }}" href="{{ route('reportes.show', 'paradas-omitidas') }}">
            <i class="bi bi-file-earmark-bar-graph" aria-hidden="true"></i><span>Paradas Omitidas</span>
        </a>
    </div>
    @endif

    <div class="menu-title">Configuración</div>
    <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}"
       @if(request()->routeIs('profile.edit')) aria-current="page" @endif>
        <i class="bi bi-gear" aria-hidden="true"></i><span>Perfil</span>
    </a>
    <form method="POST" action="{{ route('logout') }}" class="mt-2">
        @csrf
        <button type="submit" class="nav-link border-0 w-100 text-start bg-transparent">
            <i class="bi bi-box-arrow-right" aria-hidden="true"></i><span>Cerrar sesión</span>
        </button>
    </form>
</aside>
