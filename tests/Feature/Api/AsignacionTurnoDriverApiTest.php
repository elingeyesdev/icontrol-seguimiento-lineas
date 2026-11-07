<?php

namespace Tests\Feature\Api;

use App\Models\AsignacionTurno;
use App\Models\Conductor;
use App\Models\Interno;
use App\Models\Micro;
use App\Models\Propietario;
use App\Models\Ruta;
use App\Models\Turno;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AsignacionTurnoDriverApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $userConductor;

    protected Conductor $conductor;

    protected Turno $turno;

    protected Ruta $ruta;

    protected Micro $micro;

    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Crear usuario y conductor
        $this->userConductor = User::factory()->create([
            'name' => 'Carlos',
            'apellido' => 'Gomez',
            'email' => 'carlos.gomez@linea61.com',
            'password' => 'password123',
        ]);
        Role::findOrCreate('conductor', 'web');
        $this->userConductor->assignRole('conductor');

        $this->conductor = Conductor::create([
            'user_id' => $this->userConductor->id,
            'licencia' => 'LIC-7890',
            'estado' => 'activo',
        ]);

        // 2. Crear parametrización
        $this->turno = Turno::create([
            'nombre' => 'mañana',
            'hora_inicio' => '06:00:00',
            'hora_fin' => '14:00:00',
            'estado' => 'activo',
        ]);

        $this->ruta = Ruta::create([
            'nombre' => 'Ruta Troncal 61',
            'descripcion' => 'Recorrido Norte - Sur',
            'estado' => 'activo',
        ]);

        $userPropietario = User::factory()->create();
        $propietario = Propietario::create([
            'user_id' => $userPropietario->id,
            'estado' => 'activo',
        ]);

        $interno = Interno::create([
            'numero_interno' => '101',
            'fecha_ingreso' => now()->toDateString(),
            'estado' => 'disponible',
        ]);

        $this->micro = Micro::create([
            'propietario_id' => $propietario->id,
            'interno_id' => $interno->id,
            'placa' => '4567-XYZ',
            'marca' => 'Toyota',
            'modelo' => 'Coaster',
            'capacidad_pasajeros' => 30,
            'estado' => 'activo',
        ]);

        $tokenResponse = $this->postJson('/api/login', [
            'email' => 'carlos.gomez@linea61.com',
            'password' => 'password123',
        ]);

        $this->token = $tokenResponse->json('access_token');
    }

    public function test_conductor_can_see_assigned_shifts(): void
    {
        // Crear asignación para el conductor
        $asignacion = AsignacionTurno::create([
            'fecha' => now()->toDateString(),
            'turno_id' => $this->turno->id,
            'ruta_id' => $this->ruta->id,
            'micro_id' => $this->micro->id,
            'conductor_id' => $this->conductor->id,
            'estado' => 'pendiente',
        ]);

        // Consultar /api/mis/asignaciones
        $response = $this->withToken($this->token)
            ->getJson('/api/mis/asignaciones')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('conductor.id', $this->conductor->id);

        $this->assertSame($asignacion->id, $response->json('asignaciones.0.id'));
    }

    public function test_conductor_can_get_current_active_or_pending_shift(): void
    {
        $asignacion = AsignacionTurno::create([
            'fecha' => now()->toDateString(),
            'turno_id' => $this->turno->id,
            'ruta_id' => $this->ruta->id,
            'micro_id' => $this->micro->id,
            'conductor_id' => $this->conductor->id,
            'estado' => 'pendiente',
        ]);

        $response = $this->withToken($this->token)
            ->getJson('/api/mis/asignacion-actual')
            ->assertOk()
            ->assertJsonPath('asignacion.id', $asignacion->id)
            ->assertJsonPath('asignacion.estado', 'pendiente');
    }

    public function test_conductor_can_start_and_complete_shift(): void
    {
        $asignacion = AsignacionTurno::create([
            'fecha' => now()->toDateString(),
            'turno_id' => $this->turno->id,
            'ruta_id' => $this->ruta->id,
            'micro_id' => $this->micro->id,
            'conductor_id' => $this->conductor->id,
            'estado' => 'pendiente',
        ]);

        // 1. Iniciar turno
        $startResponse = $this->withToken($this->token)
            ->postJson("/api/mis/asignaciones/{$asignacion->id}/iniciar")
            ->assertOk()
            ->assertJsonPath('asignacion.estado', 'en_curso');

        $this->assertNotNull($startResponse->json('asignacion.hora_salida'));
        $this->assertDatabaseHas('asignacion_turnos', [
            'id' => $asignacion->id,
            'estado' => 'en_curso',
        ]);

        // 2. Finalizar turno
        $finishResponse = $this->withToken($this->token)
            ->postJson("/api/mis/asignaciones/{$asignacion->id}/finalizar")
            ->assertOk()
            ->assertJsonPath('asignacion.estado', 'completado');

        $this->assertNotNull($finishResponse->json('asignacion.hora_llegada'));
        $this->assertDatabaseHas('asignacion_turnos', [
            'id' => $asignacion->id,
            'estado' => 'completado',
        ]);
    }

    public function test_conductor_cannot_start_another_conductors_shift(): void
    {
        $otroUser = User::factory()->create();
        $otroConductor = Conductor::create([
            'user_id' => $otroUser->id,
            'licencia' => 'LIC-1111',
            'estado' => 'activo',
        ]);

        $asignacionAjena = AsignacionTurno::create([
            'fecha' => now()->toDateString(),
            'turno_id' => $this->turno->id,
            'ruta_id' => $this->ruta->id,
            'micro_id' => $this->micro->id,
            'conductor_id' => $otroConductor->id,
            'estado' => 'pendiente',
        ]);

        // Intentar iniciar con el token de Carlos Gomez
        $this->withToken($this->token)
            ->postJson("/api/mis/asignaciones/{$asignacionAjena->id}/iniciar")
            ->assertStatus(422)
            ->assertJsonValidationErrors(['conductor']);
    }

    public function test_user_without_conductor_role_cannot_use_driver_api(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $token = $user->createToken('mobile')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/mis/asignaciones')
            ->assertForbidden();
    }
}
