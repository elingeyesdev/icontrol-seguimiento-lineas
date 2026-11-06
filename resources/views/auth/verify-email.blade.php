<x-guest-layout title="Verificar correo">
    <div class="auth-single">
        <div class="auth-card">
            <div class="auth-card-brand">
                <x-auth-brand compact />
            </div>

            <x-auth-heading
                align="center"
                icon="bi-envelope-check"
                eyebrow="Verificación"
                title="Verifica tu correo"
                description="Enviamos un enlace de confirmación a tu dirección de correo electrónico."
            />

            @if (session('status') == 'verification-link-sent')
                <x-auth-status type="success">
                    Se envió un nuevo enlace de verificación a tu correo.
                </x-auth-status>
            @endif

            <div class="auth-actions">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf

                    <button type="submit" class="auth-btn auth-btn--primary auth-btn--block">
                        <i class="bi bi-arrow-clockwise" aria-hidden="true"></i>
                        Reenviar correo de verificación
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit" class="auth-btn auth-btn--ghost auth-btn--block">
                        <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
