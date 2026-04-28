<x-guest-layout>
    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success mb-3" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input id="password" class="form-control @error('password') is-invalid @enderror" type="password" name="password" required autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="mb-3 form-check">
            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
            <label class="form-check-label" for="remember_me">Recordarme</label>
        </div>

        <div class="d-grid gap-2 mb-3">
            <button type="submit" class="btn btn-primary">
                Iniciar Sesión
            </button>
        </div>

        <div class="text-center">
            @if (Route::has('password.request'))
                <a class="text-decoration-none small" href="{{ route('password.request') }}">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>
        
        @if (Route::has('register'))
            <div class="text-center mt-2">
                <a class="text-decoration-none small text-muted" href="{{ route('register') }}">
                    ¿No tienes cuenta? Regístrate aquí
                </a>
            </div>
        @endif
    </form>
</x-guest-layout>
