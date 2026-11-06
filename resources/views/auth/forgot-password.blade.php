<x-guest-layout title="Recuperar contraseña">
    <div class="auth-single">
        <div class="auth-card">
            <div class="auth-card-brand">
                <x-auth-brand compact />
            </div>

            <x-auth-heading
                align="center"
                icon="bi-key"
                eyebrow="Recuperación"
                title="Recuperar contraseña"
                description="Ingresa tu correo y te enviaremos el enlace para restablecerla."
            />

            @if (session('status'))
                <x-auth-status type="success">{{ session('status') }}</x-auth-status>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <x-auth-field
                    name="email"
                    label="Correo electrónico"
                    type="email"
                    icon="bi-envelope"
                    placeholder="tu@correo.com"
                    autocomplete="username"
                    :required="true"
                    :autofocus="true"
                />

                <button type="submit" class="auth-btn auth-btn--primary auth-btn--block auth-submit">
                    Enviar enlace
                    <i class="bi bi-send" aria-hidden="true"></i>
                </button>

                <div class="auth-divider"><span>o</span></div>

                <a href="{{ route('login') }}" class="auth-btn auth-btn--secondary auth-btn--block">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                    Volver a iniciar sesión
                </a>
            </form>
        </div>
    </div>
</x-guest-layout>
