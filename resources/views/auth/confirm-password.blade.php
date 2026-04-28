<x-guest-layout>
    <div class="mb-4 small text-muted">
        Esta es un área segura de la aplicación. Por favor, confirma tu contraseña antes de continuar.
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

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

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">
                Confirmar
            </button>
        </div>
    </form>
</x-guest-layout>
