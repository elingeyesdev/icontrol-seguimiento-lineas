<section>
    <header class="mb-3">
        <h2 class="section-title text-danger">Eliminar cuenta</h2>
        <p class="page-subtitle mb-0">
            Una vez eliminada la cuenta, todos sus recursos y datos se borrarán de forma permanente.
        </p>
    </header>

    <button type="button" class="btn btn-outline-danger"
            data-bs-toggle="modal" data-bs-target="#confirm-user-deletion">
        <i class="bi bi-trash me-1" aria-hidden="true"></i>Eliminar cuenta
    </button>

    <div class="modal fade" id="confirm-user-deletion" tabindex="-1" aria-labelledby="confirm-user-deletion-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="modal-header">
                        <h2 class="modal-title fs-5" id="confirm-user-deletion-title">¿Eliminar tu cuenta?</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <p>
                            Una vez eliminada la cuenta, todos sus recursos y datos se borrarán de forma permanente.
                            Ingresa tu contraseña para confirmar.
                        </p>

                        <label for="delete_account_password" class="form-label">Contraseña</label>
                        <input id="delete_account_password" name="password" type="password"
                               class="form-control @if($errors->userDeletion->has('password')) is-invalid @endif"
                               placeholder="Contraseña">
                        @if($errors->userDeletion->has('password'))
                            <div class="invalid-feedback">{{ $errors->userDeletion->first('password') }}</div>
                        @endif
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar cuenta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
