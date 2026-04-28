<nav class="navbar navbar-expand-lg navbar-dark shadow-sm" style="background-color: #1A2D4F; border-bottom: 3px solid #E07B15;">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand font-weight-bold" href="{{ route('dashboard') }}">
            Línea 57
        </a>

        <!-- Hamburger for mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        Dashboard
                    </a>
                </li>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <!-- User Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">Mi Perfil</a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                                    Cerrar Sesión
                                </a>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
