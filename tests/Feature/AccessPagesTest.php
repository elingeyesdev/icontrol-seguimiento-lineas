<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_access_pages_can_be_rendered(): void
    {
        $this->get('/login')->assertOk()->assertSee('Iniciar sesión');
        $this->get('/register')->assertOk()->assertSee('Crear cuenta');
        $this->get('/forgot-password')->assertOk()->assertSee('Recuperar contraseña');
        $this->get('/reset-password/token-de-prueba')->assertOk()->assertSee('Restablecer contraseña');
    }

    public function test_authenticated_access_pages_can_be_rendered(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get('/verify-email')
            ->assertOk()
            ->assertSee('Verifica tu correo');

        $this->actingAs($user)
            ->get('/confirm-password')
            ->assertOk()
            ->assertSee('Área protegida');
    }

    public function test_pending_role_page_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/pending-role')
            ->assertOk()
            ->assertSee('Tu perfil aún no está activo');
    }

    public function test_not_found_page_is_shown_in_spanish(): void
    {
        $response = $this->get('/ruta-que-no-existe');

        $response->assertNotFound();
        $response->assertSee('Página no encontrada');
        $response->assertDontSee('Not Found');
        $response->assertDontSee(base_path());
    }

    public function test_error_views_expose_a_clear_spanish_message(): void
    {
        $this->view('errors.403')
            ->assertSee('Acceso denegado')
            ->assertDontSee('Forbidden');

        $this->view('errors.419')
            ->assertSee('Sesión expirada');

        $this->view('errors.429')
            ->assertSee('Demasiadas solicitudes');

        $this->view('errors.500')
            ->assertSee('Error interno del servidor')
            ->assertDontSee('Server Error');

        $this->view('errors.503')
            ->assertSee('Servicio no disponible');

        $this->view('errors.4xx')
            ->assertSee('Solicitud no válida');

        $this->view('errors.5xx')
            ->assertSee('Error del servidor');
    }
}
