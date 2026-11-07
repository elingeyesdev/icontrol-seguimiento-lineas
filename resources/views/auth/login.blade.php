<x-guest-layout title="Iniciar sesión">
    <div class="auth-shell">
        <section class="auth-hero auth-hero--login" aria-label="Información del sistema">
            <div class="auth-hero-overlay" aria-hidden="true"></div>

            <div class="auth-hero-content">
                <x-auth-brand />

                <div class="auth-hero-footer">
                    <span class="auth-hero-note">
                        <i class="bi bi-broadcast" aria-hidden="true"></i>
                        Monitoreo satelital en tiempo real
                    </span>

                    <div class="auth-hero-meta">
                        <span>Santa Cruz · Bolivia</span>
                        <span>
                            <i class="bi bi-shield-check" aria-hidden="true"></i>
                            Acceso seguro
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <section class="auth-panel">
            <div class="auth-panel-inner">
                <x-auth-brand compact class="auth-panel-brand" />

                <x-auth-heading
                    eyebrow="Línea 61 · Control operativo"
                    title="Iniciar sesión"
                    description="Ingresa para administrar la operación diaria de la línea."
                />

                @if (session('status'))
                    <x-auth-status type="success">{{ session('status') }}</x-auth-status>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <x-auth-field
                        name="email"
                        label="Correo electrónico"
                        type="email"
                        icon="bi-envelope"
                        placeholder="usuario@correo.com"
                        autocomplete="username"
                        :required="true"
                        :autofocus="true"
                    />

                    <x-auth-field
                        name="password"
                        label="Contraseña"
                        type="password"
                        icon="bi-lock"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        :required="true"
                    />

                    <div class="auth-row">
                        <div class="auth-check">
                            <input id="remember_me" type="checkbox" name="remember">
                            <label for="remember_me">Recordarme</label>
                        </div>

                        @if (Route::has('password.request'))
                            <a class="auth-link" href="{{ route('password.request') }}">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="auth-btn auth-btn--primary auth-btn--block">
                        Ingresar
                        <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </button>

                    @if (Route::has('register'))
                        <div class="auth-divider"><span>o</span></div>

                        <a href="{{ route('register') }}" class="auth-btn auth-btn--secondary auth-btn--block">
                            <i class="bi bi-person-plus" aria-hidden="true"></i>
                            Crear cuenta
                        </a>
                    @endif
                </form>
            </div>
        </section>
    </div>
</x-guest-layout>
