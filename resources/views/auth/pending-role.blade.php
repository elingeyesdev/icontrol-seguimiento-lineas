<x-guest-layout title="Acceso pendiente">
    <div class="auth-single">
        <div class="auth-card auth-card--wide">
            <div class="auth-card-brand">
                <x-auth-brand compact />
            </div>

            <x-auth-heading
                align="center"
                icon="bi-hourglass-split"
                eyebrow="Acceso pendiente"
                title="Tu perfil aún no está activo"
                description="Tu correo ya fue validado, pero todavía no se te asignó un rol dentro del sistema. Mientras tanto no podrás acceder a la operación ni al panel principal."
            />

            <div class="auth-facts">
                <div class="auth-fact">
                    <span class="auth-fact-label">Correo validado</span>
                    <span class="auth-fact-value">{{ auth()->user()?->email ?? 'Sin correo registrado' }}</span>
                </div>

                <div class="auth-fact">
                    <span class="auth-fact-label">Estado del usuario</span>
                    <span class="auth-fact-value">Sin rol asignado</span>
                </div>
            </div>

            <div class="auth-section">
                <h2 class="auth-section-title">Qué sigue</h2>
                <ul class="auth-list">
                    <li>Se revisará tu perfil y se te asignará el acceso correspondiente.</li>
                    <li>Cuando el administrador confirme tu rol, podrás ingresar al sistema.</li>
                    <li>Durante este tiempo solo puedes esperar la validación del acceso.</li>
                </ul>
            </div>

            <div class="auth-section">
                <h2 class="auth-section-title">Qué puedes hacer ahora</h2>
                <ul class="auth-list">
                    <li>Revisa tu correo para confirmar que recibiste la validación.</li>
                    <li>Espera a que te asignen el rol correcto dentro del sistema.</li>
                    <li>Si tienes dudas, comunícate con administración.</li>
                </ul>
            </div>

            <div class="auth-actions">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit" class="auth-btn auth-btn--primary auth-btn--block">
                        <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                        Cerrar sesión
                    </button>
                </form>

                <a href="{{ url('/') }}" class="auth-btn auth-btn--secondary auth-btn--block">
                    <i class="bi bi-house-door" aria-hidden="true"></i>
                    Volver al inicio
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
