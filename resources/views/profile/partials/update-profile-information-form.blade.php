<section>
    <header class="mb-3">
        <h2 class="section-title">Información del perfil</h2>
        <p class="page-subtitle">Actualiza tu nombre y correo electrónico de la cuenta.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="row g-3">
            <div class="col-md-6">
                <label for="name" class="form-label">Nombre</label>
                <input id="name" name="name" type="text"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label">Correo electrónico</label>
                <input id="email" name="email" type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $user->email) }}" required autocomplete="username">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="alert alert-soft-warning mt-3 mb-0">
                <p class="mb-1">Tu correo electrónico aún no está verificado.</p>
                <button form="send-verification" class="btn btn-sm btn-outline-brand">
                    Reenviar el correo de verificación
                </button>

                @if (session('status') === 'verification-link-sent')
                    <p class="mb-0 mt-2 small">Se envió un nuevo enlace de verificación a tu correo.</p>
                @endif
            </div>
        @endif

        <div class="d-flex align-items-center gap-3 mt-3">
            <button type="submit" class="btn btn-brand">Guardar cambios</button>

            @if (session('status') === 'profile-updated')
                <span class="badge-status badge-status--active" role="status">
                    <i class="bi bi-check-circle" aria-hidden="true"></i>Datos guardados
                </span>
            @endif
        </div>
    </form>
</section>
