<nav class="navbar navbar-expand navbar-light bg-white border-bottom shadow-sm px-3" style="height: 70px;">
    <div class="container-fluid">
        <!-- Mobile Sidebar Toggle -->
        <button class="btn btn-outline-secondary d-md-none me-2" onclick="toggleSidebar()">
            <i class="bi bi-list fs-5"></i>
        </button>
        
        <!-- Navbar Subtitle -->
        <div class="d-none d-md-block fw-semibold text-muted">
            <i class="bi bi-arrow-right-left text-warning-custom me-1"></i> Control de Seguimiento
        </div>

        <!-- User Profile Dropdown -->
        <div class="ms-auto">
            <div class="dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center text-decoration-none" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="text-white rounded-circle d-flex align-items-center justify-content-center me-2 fw-bold" style="width: 35px; height: 35px; background-color: #1A2D4F;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span class="d-none d-sm-inline fw-semibold text-dark">{{ Auth::user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="navbarDropdown" style="border-radius: 8px;">
                    <li>
                        <a class="dropdown-item py-2" href="{{ route('profile.edit') }}">
                            <i class="bi bi-person me-2 text-muted"></i> Mi Perfil
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a class="dropdown-item py-2 text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                            </a>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
