<x-guest-layout>
    <div class="mb-4 small text-muted">
        ¡Gracias por registrarte! Antes de comenzar, ¿podrías verificar tu dirección de correo electrónico haciendo clic en el enlace que te acabamos de enviar? Si no recibiste el correo, con gusto te enviaremos otro.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success mb-3" role="alert">
            Se ha enviado un nuevo enlace de verificación a la dirección de correo electrónico que proporcionaste durante el registro.
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mt-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm">
                Reenviar correo de verificación
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link btn-sm text-decoration-none text-muted">
                Cerrar sesión
            </button>
        </form>
    </div>
</x-guest-layout>
