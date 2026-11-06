<x-guest-layout title="Restablecer contraseña">
    <div class="auth-single">
        <div class="auth-card">
            <div class="auth-card-brand">
                <x-auth-brand compact />
            </div>

            <x-auth-heading
                align="center"
                icon="bi-shield-lock"
                eyebrow="Nueva clave"
                title="Restablecer contraseña"
                description="Escribe tu nueva contraseña de acceso."
            />

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <x-auth-field
                    name="email"
                    label="Correo electrónico"
                    type="email"
                    icon="bi-envelope"
                    :value="$request->email"
                    autocomplete="username"
                    :required="true"
                    :autofocus="true"
                />

                <x-auth-field
                    name="password"
                    label="Nueva contraseña"
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

                <button type="submit" class="auth-btn auth-btn--primary auth-btn--block auth-submit">
                    Guardar contraseña
                    <i class="bi bi-check-circle" aria-hidden="true"></i>
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
