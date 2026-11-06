<x-guest-layout title="Crear cuenta">
    <div class="auth-shell">
        <section class="auth-hero auth-hero--register" aria-label="Información del registro">
            <div class="auth-hero-overlay" aria-hidden="true"></div>

            <div class="auth-hero-content">
                <x-auth-brand />

                <div class="auth-hero-body">
                    <p class="auth-hero-eyebrow">Registro de personal</p>
                    <p class="auth-hero-lead">Crea tu cuenta para incorporarte a la operación de la Línea 61.</p>

                    <ul class="auth-hero-list">
                        <li>
                            <i class="bi bi-check2" aria-hidden="true"></i>
                            Monitoreo satelital GPS en vivo
                        </li>
                        <li>
                            <i class="bi bi-check2" aria-hidden="true"></i>
                            Control de paradas y tiempos
                        </li>
                        <li>
                            <i class="bi bi-check2" aria-hidden="true"></i>
                            Gestión de flota e internos
                        </li>
                    </ul>
                </div>

                <div class="auth-hero-meta">
                    <span>Santa Cruz · Bolivia</span>
                    <span>
                        <i class="bi bi-shield-lock" aria-hidden="true"></i>
                        Registro seguro
                    </span>
                </div>
            </div>
        </section>

        <section class="auth-panel">
            <div class="auth-panel-inner auth-panel-inner--wide">
                <x-auth-brand compact class="auth-panel-brand" />

                <x-auth-heading
                    eyebrow="Registro de usuario"
                    title="Crear cuenta"
                    description="Completa tus datos para registrar un nuevo usuario en la plataforma."
                />

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="auth-grid">
                        <x-auth-field
                            name="name"
                            label="Nombres"
                            icon="bi-person"
                            placeholder="Ej. Carlos"
                            autocomplete="given-name"
                            :required="true"
                            :autofocus="true"
                        />

                        <x-auth-field
                            name="apellido"
                            label="Apellidos"
                            icon="bi-person"
                            placeholder="Ej. Rojas Pérez"
                            autocomplete="family-name"
                            :required="true"
                        />

                        <x-auth-field
                            name="ci"
                            label="Carnet de identidad (CI)"
                            icon="bi-card-text"
                            placeholder="Ej. 8492019 SC"
                            :required="true"
                        />

                        <x-auth-field
                            name="telefono"
                            label="Teléfono o celular"
                            icon="bi-telephone"
                            placeholder="Ej. 70912345"
                            autocomplete="tel"
                            :required="true"
                        />

                        <x-auth-field
                            class="auth-grid--full"
                            name="email"
                            label="Correo electrónico"
                            type="email"
                            icon="bi-envelope"
                            placeholder="usuario@correo.com"
                            autocomplete="username"
                            :required="true"
                        />

                        <x-auth-field
                            name="password"
                            label="Contraseña"
                            type="password"
                            icon="bi-lock"
                            placeholder="Mínimo 8 caracteres"
                            autocomplete="new-password"
                            hint="Usa al menos 8 caracteres."
                            :required="true"
                        />

                        <x-auth-field
                            name="password_confirmation"
                            label="Confirmar contraseña"
                            type="password"
                            icon="bi-shield-lock"
                            placeholder="Repite la contraseña"
                            autocomplete="new-password"
                            :required="true"
                        />
                    </div>

                    <button type="submit" class="auth-btn auth-btn--primary auth-btn--block auth-submit">
                        Registrarme
                        <i class="bi bi-check-circle" aria-hidden="true"></i>
                    </button>

                    <div class="auth-divider"><span>o</span></div>

                    <a href="{{ route('login') }}" class="auth-btn auth-btn--secondary auth-btn--block">
                        <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                        Iniciar sesión
                    </a>
                </form>
            </div>
        </section>
    </div>
</x-guest-layout>
