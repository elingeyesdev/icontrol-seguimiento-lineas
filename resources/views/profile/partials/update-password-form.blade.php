<section>
    <header class="mb-3">
        <h2 class="section-title">Cambiar contraseña</h2>
        <p class="page-subtitle">Usa una contraseña larga y difícil de adivinar para mantener tu cuenta segura.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="row g-3">
            <div class="col-12">
                <label for="update_password_current_password" class="form-label">Contraseña actual</label>
                <input id="update_password_current_password" name="current_password" type="password"
                       class="form-control @if($errors->updatePassword->has('current_password')) is-invalid @endif"
                       autocomplete="current-password">
                @if($errors->updatePassword->has('current_password'))
                    <div class="invalid-feedback">{{ $errors->updatePassword->first('current_password') }}</div>
                @endif
            </div>

            <div class="col-md-6">
                <label for="update_password_password" class="form-label">Nueva contraseña</label>
                <input id="update_password_password" name="password" type="password"
                       class="form-control @if($errors->updatePassword->has('password')) is-invalid @endif"
                       autocomplete="new-password">
                @if($errors->updatePassword->has('password'))
                    <div class="invalid-feedback">{{ $errors->updatePassword->first('password') }}</div>
                @endif
            </div>

            <div class="col-md-6">
                <label for="update_password_password_confirmation" class="form-label">Confirmar contraseña</label>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                       class="form-control @if($errors->updatePassword->has('password_confirmation')) is-invalid @endif"
                       autocomplete="new-password">
                @if($errors->updatePassword->has('password_confirmation'))
                    <div class="invalid-feedback">{{ $errors->updatePassword->first('password_confirmation') }}</div>
                @endif
            </div>
        </div>

        <div class="d-flex align-items-center gap-3 mt-3">
            <button type="submit" class="btn btn-brand">Actualizar contraseña</button>

            @if (session('status') === 'password-updated')
                <span class="badge-status badge-status--active" role="status">
                    <i class="bi bi-check-circle" aria-hidden="true"></i>Contraseña actualizada
                </span>
            @endif
        </div>
    </form>
</section>
