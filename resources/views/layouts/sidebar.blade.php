<aside id="sidebar" class="sidebar d-flex flex-column text-white">
    <!-- Brand Logo -->
    <div class="p-3 d-flex align-items-center justify-content-between border-bottom border-secondary border-opacity-25" style="height: 70px;">
        <a href="{{ route('dashboard') }}" class="text-white text-decoration-none d-flex align-items-center">
            <i class="bi bi-bus-front-fill me-2 fs-4 text-warning-custom"></i>
            <span class="fs-5 fw-bold tracking-wide">Línea 57</span>
        </a>
        <button class="btn btn-sm btn-link text-white d-md-none p-0" onclick="toggleSidebar()">
            <i class="bi bi-x-lg fs-5"></i>
        </button>
    </div>

    <!-- Menu Items -->
    <div class="flex-grow-1 overflow-y-auto py-3">
        <!-- Dashboard -->
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>

        <!-- Acceso y Seguridad -->
        <div class="menu-header">Acceso y Seguridad</div>
        <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
            <i class="bi bi-people-fill me-2"></i> Usuarios
        </a>
        <a class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
            <i class="bi bi-shield-lock-fill me-2"></i> Roles
        </a>

        <!-- Parametrización -->
        <div class="menu-header">Parametrización</div>
        <a class="nav-link {{ request()->routeIs('conductor.*') ? 'active' : '' }}" href="{{ route('conductor.index') }}">
            <i class="bi bi-person-badge-fill me-2"></i> Conductores
        </a>
        <a class="nav-link {{ request()->routeIs('dueño.*') ? 'active' : '' }}" href="{{ route('dueño.index') }}">
            <i class="bi bi-person-workspace me-2"></i> Dueños
        </a>
        <a class="nav-link {{ request()->routeIs('micro.*') ? 'active' : '' }}" href="{{ route('micro.index') }}">
            <i class="bi bi-truck me-2"></i> Micros
        </a>
        <a class="nav-link {{ request()->routeIs('interno.*') ? 'active' : '' }}" href="{{ route('interno.index') }}">
            <i class="bi bi-hash me-2"></i> Internos
        </a>
        <a class="nav-link {{ request()->routeIs('ruta.*') ? 'active' : '' }}" href="{{ route('ruta.index') }}">
            <i class="bi bi-geo-alt-fill me-2"></i> Rutas
        </a>

        <!-- Transaccional -->
        <div class="menu-header">Transaccional</div>
        <a class="nav-link {{ request()->routeIs('turno.*') ? 'active' : '' }}" href="{{ route('turno.index') }}">
            <i class="bi bi-calendar-event-fill me-2"></i> Turnos
        </a>

        <!-- Reporte -->
        <div class="menu-header">Reportes</div>
        <a class="nav-link" href="#">
            <i class="bi bi-bar-chart-line-fill me-2"></i> Reportes Generales
        </a>

        <!-- Estadísticas y KPIs -->
        <div class="menu-header">Estadísticas y KPIs</div>
        <a class="nav-link {{ request()->routeIs('kpis.tiempo-promedio-recorrido.*') ? 'active' : '' }}" href="{{ route('kpis.tiempo-promedio-recorrido.index') }}">
            <i class="bi bi-stopwatch me-2"></i> Tiempo promedio de recorrido
        </a>
        <a class="nav-link {{ request()->routeIs('kpis.cumplimiento-paradas.*') ? 'active' : '' }}" href="{{ route('kpis.cumplimiento-paradas.index') }}">
            <i class="bi bi-check2-circle me-2"></i> Cumplimiento de paradas
        </a>
    </div>
    
    <!-- Footer / Info -->
    <div class="p-3 border-top border-secondary border-opacity-25 small text-center text-white-50">
        &copy; {{ date('Y') }} Línea 57
    </div>
</aside>
