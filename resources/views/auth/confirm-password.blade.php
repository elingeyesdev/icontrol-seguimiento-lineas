<x-guest-layout title="Confirmar contraseña">
    <div class="auth-single">
        <div class="auth-card">
            <div class="auth-card-brand">
                <x-auth-brand compact />
            </div>

            <x-auth-heading
                align="center"
                icon="bi-shield-lock"
                eyebrow="Confirmación"
                title="Área protegida"
                description="Por seguridad, confirma tu contraseña antes de continuar."
            />

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <x-auth-field
                    name="password"
                    label="Contraseña"
                    type="password"
                    icon="bi-lock"
                    placeholder="••••••••"
                    autocomplete="current-password"
                    :required="true"
                />

                <button type="submit" class="auth-btn auth-btn--primary auth-btn--block auth-submit">
                    Confirmar
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
