<x-guest-layout>
    <div class="mb-4 small text-muted">
        ¿Olvidaste tu contraseña? No hay problema. Simplemente déjanos saber tu dirección de correo electrónico y te enviaremos un enlace de restablecimiento de contraseña que te permitirá elegir una nueva.
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success mb-3" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">
                Enviar enlace al correo
            </button>
        </div>
        
        <div class="text-center mt-3">
            <a class="text-decoration-none small text-muted" href="{{ route('login') }}">
                Volver al inicio de sesión
            </a>
        </div>
    </form>
</x-guest-layout>
