<?php

namespace Tests\Feature\Api;

use App\Models\AsignacionTurno;
use App\Models\Conductor;
use App\Models\ControlRecorrido;
use App\Models\Interno;
use App\Models\Micro;
use App\Models\Parada;
use App\Models\Propietario;
use App\Models\Ruta;
use App\Models\RutaParada;
use App\Models\Turno;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SeguimientoGpsApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Conductor $conductor;

    protected AsignacionTurno $asignacion;

    protected Ruta $ruta;

    protected Parada $parada1;

    protected Parada $parada2;

    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'conductor.gps@linea61.com',
            'password' => 'secret123',
        ]);
        Role::findOrCreate('conductor', 'web');
        $this->user->assignRole('conductor');

        $this->conductor = Conductor::create([
            'user_id' => $this->user->id,
            'licencia' => 'LIC-GPS-99',
            'estado' => 'activo',
        ]);

        $turno = Turno::create([
            'nombre' => 'mañana',
            'hora_inicio' => '06:00:00',
            'hora_fin' => '14:00:00',
            'estado' => 'activo',
        ]);

        $this->ruta = Ruta::create([
            'nombre' => 'Ruta GPS Test',
            'estado' => 'activo',
        ]);

        // Crear dos paradas en la ruta
        $this->parada1 = Parada::create([
            'nombre' => 'Parada 1 Inicial',
            'latitud' => -17.78300000,
            'longitud' => -63.18200000,
            'estado' => 'activo',
        ]);

        $this->parada2 = Parada::create([
            'nombre' => 'Parada 2 Final',
            'latitud' => -17.79000000,
            'longitud' => -63.19000000,
            'estado' => 'activo',
        ]);

        RutaParada::create([
            'ruta_id' => $this->ruta->id,
            'parada_id' => $this->parada1->id,
            'orden' => 1,
            'sentido' => 'Ida',
            'estado' => 'activo',
        ]);

        RutaParada::create([
            'ruta_id' => $this->ruta->id,
            'parada_id' => $this->parada2->id,
            'orden' => 2,
            'sentido' => 'Ida',
            'estado' => 'activo',
        ]);

        $userProp = User::factory()->create();
        $prop = Propietario::create(['user_id' => $userProp->id, 'estado' => 'activo']);
        $interno = Interno::create(['numero_interno' => 'GPS-01', 'fecha_ingreso' => now()->toDateString(), 'estado' => 'disponible']);
        $micro = Micro::create([
            'propietario_id' => $prop->id,
            'interno_id' => $interno->id,
            'placa' => '9999-GPS',
            'marca' => 'Toyota',
            'modelo' => 'Coaster',
            'capacidad_pasajeros' => 25,
            'estado' => 'activo',
        ]);

        $this->asignacion = AsignacionTurno::create([
            'fecha' => now()->toDateString(),
            'turno_id' => $turno->id,
            'ruta_id' => $this->ruta->id,
            'micro_id' => $micro->id,
            'conductor_id' => $this->conductor->id,
            'estado' => 'en_curso',
            'hora_salida' => '06:05:00',
        ]);

        $login = $this->postJson('/api/login', [
            'email' => 'conductor.gps@linea61.com',
            'password' => 'secret123',
        ]);

        $this->token = $login->json('access_token');
    }

    public function test_conductor_can_report_gps_point(): void
    {
        $timestamp = now()->subMinutes(1)->toDateTimeString();

        $response = $this->withToken($this->token)
            ->postJson("/api/mis/asignaciones/{$this->asignacion->id}/ubicaciones", [
                'fecha_hora_gps' => $timestamp,
                'latitud' => -17.78301000,
                'longitud' => -63.18201000,
                'velocidad' => 25.5,
            ])
            ->assertCreated()
            ->assertJsonPath('message', 'Ubicación procesada correctamente.');

        $controlCumplido = $this->asignacion->controlesRecorrido()
            ->where('estado', 'cumplido')
            ->firstOrFail();

        $this->assertDatabaseHas('seguimiento_gps', [
            'control_recorrido_id' => $controlCumplido->id,
            'latitud' => -17.78301000,
        ]);
        $this->assertSame(1, $this->asignacion->seguimientosGps()->count());
    }

    public function test_duplicate_gps_point_is_deduplicated_safely(): void
    {
        $timestamp = now()->subMinutes(2)->toDateTimeString();

        $payload = [
            'fecha_hora_gps' => $timestamp,
            'latitud' => -17.78000000,
            'longitud' => -63.18000000,
            'velocidad' => 20.0,
        ];

        // Primer envío
        $this->withToken($this->token)
            ->postJson("/api/mis/asignaciones/{$this->asignacion->id}/ubicaciones", $payload)
            ->assertCreated();

        // Segundo envío idéntico (reintento offline)
        $this->withToken($this->token)
            ->postJson("/api/mis/asignaciones/{$this->asignacion->id}/ubicaciones", $payload)
            ->assertCreated();

        // Sólo debe existir 1 registro en BD
        $this->assertSame(1, $this->asignacion->seguimientosGps()->count());
    }

    public function test_dashboard_resolves_conductor_name_from_user_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Ana',
            'apellido' => 'Gomez',
            'email' => 'ana.gomez@linea61.com',
        ]);

        $conductor = Conductor::create([
            'user_id' => $user->id,
            'licencia' => 'LIC-ANA-01',
            'nombre' => null,
            'apellido' => null,
            'telefono' => null,
            'correo' => null,
            'ci' => null,
            'estado' => 'activo',
        ]);

        $this->assertSame('Ana Gomez', $conductor->nombreCompleto());
    }

    public function test_reaching_final_stop_automatically_completes_shift(): void
    {
        // 1. Llegar a la parada 1 (inicial)
        $this->withToken($this->token)
            ->postJson("/api/mis/asignaciones/{$this->asignacion->id}/ubicaciones", [
                'fecha_hora_gps' => now()->subMinutes(5)->toDateTimeString(),
                'latitud' => -17.78300500,
                'longitud' => -63.18200500,
                'velocidad' => 10.0,
            ])
            ->assertCreated();

        $this->assertDatabaseHas('control_recorrido', [
            'asignacion_turno_id' => $this->asignacion->id,
            'estado' => 'cumplido',
        ]);
        $this->assertSame(1, $this->asignacion->controlesRecorrido()->where('estado', 'cumplido')->count());

        $this->assertSame('en_curso', $this->asignacion->fresh()->estado);

        // 2. Llegar a la parada 2 (final) -> Cierre automático (SDD Sección 13)
        $this->withToken($this->token)
            ->postJson("/api/mis/asignaciones/{$this->asignacion->id}/ubicaciones", [
                'fecha_hora_gps' => now()->subMinutes(1)->toDateTimeString(),
                'latitud' => -17.79000500,
                'longitud' => -63.19000500,
                'velocidad' => 0.0,
            ])
            ->assertCreated();

        $freshAsignacion = $this->asignacion->fresh();
        $this->assertSame('completado', $freshAsignacion->estado);
        $this->assertNotNull($freshAsignacion->hora_llegada);
    }

    public function test_conductor_can_sync_batch_of_offline_locations(): void
    {
        $batch = [
            'asignacion_turno_id' => $this->asignacion->id,
            'ubicaciones' => [
                [
                    'fecha_hora_gps' => now()->subMinutes(10)->toDateTimeString(),
                    'latitud' => -17.78100000,
                    'longitud' => -63.18100000,
                    'velocidad' => 30.0,
                ],
                [
                    'fecha_hora_gps' => now()->subMinutes(8)->toDateTimeString(),
                    'latitud' => -17.78200000,
                    'longitud' => -63.18200000,
                    'velocidad' => 28.0,
                ],
            ],
        ];

        $response = $this->withToken($this->token)
            ->postJson('/api/mis/ubicaciones/sincronizar', $batch)
            ->assertOk()
            ->assertJsonPath('resultado.total_procesados', 2)
            ->assertJsonPath('resultado.guardados', 2);

        $this->assertSame(2, $this->asignacion->seguimientosGps()->count());
    }

    public function test_conductor_cannot_query_another_conductors_route(): void
    {
        $otherUser = User::factory()->create([
            'email' => 'otro.conductor@linea61.com',
            'password' => 'secret123',
        ]);
        $otherUser->assignRole('conductor');
        Conductor::create([
            'user_id' => $otherUser->id,
            'licencia' => 'LIC-GPS-OTHER',
            'estado' => 'activo',
        ]);

        $otherLogin = $this->postJson('/api/login', [
            'email' => 'otro.conductor@linea61.com',
            'password' => 'secret123',
        ])->assertOk();

        $this->withToken($otherLogin->json('access_token'))
            ->getJson("/api/mis/asignaciones/{$this->asignacion->id}/recorrido")
            ->assertForbidden()
            ->assertJsonPath('message', 'No tienes autorización para consultar esta asignación.');
    }

    public function test_conductor_can_query_own_route_status_for_mobile(): void
    {
        $rutaParada = RutaParada::where('ruta_id', $this->ruta->id)
            ->where('orden', 1)
            ->firstOrFail();

        ControlRecorrido::create([
            'asignacion_turno_id' => $this->asignacion->id,
            'ruta_parada_id' => $rutaParada->id,
            'fecha_hora' => now()->subMinute(),
            'estado' => 'cumplido',
            'distancia_metros' => 4.5,
            'observacion' => 'Parada detectada por GPS',
        ]);

        $response = $this->withToken($this->token)
            ->getJson("/api/mis/asignaciones/{$this->asignacion->id}/recorrido")
            ->assertOk()
            ->assertJsonPath('asignacion.controles_recorrido.0.estado', 'cumplido')
            ->assertJsonPath('asignacion.controles_recorrido.0.ruta_parada.parada.nombre', 'Parada 1 Inicial')
            ->assertJsonPath('paradas_cumplidas.0.estado', 'cumplido');

        $this->assertSame($this->asignacion->id, $response->json('asignacion.id'));
    }
}
