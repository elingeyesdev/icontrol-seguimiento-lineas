<?php

namespace Tests\Feature;

use App\Models\AsignacionTurno;
use App\Models\Conductor;
use App\Models\ControlRecorrido;
use App\Models\Micro;
use App\Models\Parada;
use App\Models\Propietario;
use App\Models\Ruta;
use App\Models\RutaParada;
use App\Models\SeguimientoGps;
use App\Models\Turno;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Pruebas de caja negra de los flujos transaccionales principales:
 * alta de asignaciones, ciclo de vida del turno, ingesta GPS y control
 * de recorrido. Todos los casos se ejecutan contra los endpoints HTTP.
 */
class CajaNegraModulosTransaccionalesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $conductorUser;

    protected Conductor $conductor;

    protected User $otroConductorUser;

    protected Conductor $otroConductor;

    protected Turno $turnoActivo;

    protected Turno $turnoInactivo;

    protected Ruta $ruta;

    protected Parada $parada1;

    protected Parada $parada2;

    protected Micro $micro1;

    protected Micro $micro2;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $this->admin = User::factory()->create([
            'email' => 'admin.caja.negra@linea61.com',
            'password' => 'secret123',
        ]);
        $this->admin->assignRole($role);

        $this->conductorUser = User::factory()->create([
            'name' => 'Carlos',
            'apellido' => 'Gomez',
            'email' => 'conductor.caja.negra@linea61.com',
            'password' => 'secret123',
        ]);
        $this->conductor = Conductor::create([
            'user_id' => $this->conductorUser->id,
            'licencia' => 'LIC-CN-01',
            'estado' => 'activo',
        ]);

        $this->otroConductorUser = User::factory()->create([
            'name' => 'Ana',
            'apellido' => 'Rojas',
            'email' => 'otro.conductor.caja.negra@linea61.com',
            'password' => 'secret123',
        ]);
        $this->otroConductor = Conductor::create([
            'user_id' => $this->otroConductorUser->id,
            'licencia' => 'LIC-CN-02',
            'estado' => 'activo',
        ]);

        $this->turnoActivo = Turno::create([
            'nombre' => 'mañana',
            'hora_inicio' => '06:00:00',
            'hora_fin' => '14:00:00',
            'estado' => 'activo',
        ]);

        $this->turnoInactivo = Turno::create([
            'nombre' => 'tarde',
            'hora_inicio' => '14:00:00',
            'hora_fin' => '22:00:00',
            'estado' => 'inactivo',
        ]);

        $this->ruta = Ruta::create([
            'nombre' => 'Ruta Troncal 61',
            'estado' => 'activo',
        ]);

        $this->parada1 = Parada::create([
            'nombre' => 'Parada 1',
            'latitud' => -17.78300000,
            'longitud' => -63.18200000,
            'estado' => 'activo',
        ]);

        $this->parada2 = Parada::create([
            'nombre' => 'Parada 2',
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

        $propietario = Propietario::create([
            'nombre' => 'Carlos',
            'apellido' => 'Suarez',
            'telefono' => '71111111',
            'correo' => 'propietario.caja.negra@linea61.com',
            'ci' => '12345678',
            'estado' => 'activo',
        ]);

        $this->micro1 = Micro::create([
            'propietario_id' => $propietario->id,
            'placa' => '4567-XYZ',
            'marca' => 'Toyota',
            'modelo' => 'Coaster',
            'capacidad_pasajeros' => 30,
            'estado' => 'activo',
        ]);

        $this->micro2 = Micro::create([
            'propietario_id' => $propietario->id,
            'placa' => '8910-ABC',
            'marca' => 'Toyota',
            'modelo' => 'Coaster',
            'capacidad_pasajeros' => 30,
            'estado' => 'activo',
        ]);
    }

    // -----------------------------------------------------------------
    // Módulo A. Asignación de turnos
    // -----------------------------------------------------------------

    public function test_cn_a_pe06_alta_valida_crea_asignacion_pendiente_e_ignora_estado_y_horas(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->postJson('/api/asignacion-turnos', [
            'fecha' => now()->toDateString(),
            'turno_id' => $this->turnoActivo->id,
            'ruta_id' => $this->ruta->id,
            'micro_id' => $this->micro1->id,
            'conductor_id' => $this->conductor->id,
            'estado' => 'en_curso',
            'hora_salida' => '05:00',
            'hora_llegada' => '13:00',
        ]);

        $response->assertCreated()
            ->assertJsonPath('estado', 'pendiente')
            ->assertJsonPath('hora_salida', null)
            ->assertJsonPath('hora_llegada', null);

        $this->assertDatabaseHas('asignacion_turnos', [
            'conductor_id' => $this->conductor->id,
            'micro_id' => $this->micro1->id,
            'estado' => 'pendiente',
            'hora_salida' => null,
            'hora_llegada' => null,
        ]);
    }

    public function test_cn_a_pe01_alta_rechaza_fecha_con_formato_invalido(): void
    {
        Sanctum::actingAs($this->admin);

        $this->postJson('/api/asignacion-turnos', [
            'fecha' => '22/10/2026',
            'turno_id' => $this->turnoActivo->id,
            'ruta_id' => $this->ruta->id,
            'micro_id' => $this->micro1->id,
            'conductor_id' => $this->conductor->id,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['fecha']);

        $this->assertDatabaseCount('asignacion_turnos', 0);
    }

    public function test_cn_a_pe03_alta_rechaza_turno_inactivo(): void
    {
        Sanctum::actingAs($this->admin);

        $this->postJson('/api/asignacion-turnos', [
            'fecha' => now()->toDateString(),
            'turno_id' => $this->turnoInactivo->id,
            'ruta_id' => $this->ruta->id,
            'micro_id' => $this->micro1->id,
            'conductor_id' => $this->conductor->id,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['turno_id']);

        $this->assertDatabaseCount('asignacion_turnos', 0);
    }

    public function test_cn_a_pe04_alta_rechaza_conductor_duplicado_en_fecha_y_turno(): void
    {
        $this->crearAsignacion(['micro_id' => $this->micro1->id]);

        Sanctum::actingAs($this->admin);

        $this->postJson('/api/asignacion-turnos', [
            'fecha' => now()->toDateString(),
            'turno_id' => $this->turnoActivo->id,
            'ruta_id' => $this->ruta->id,
            'micro_id' => $this->micro2->id,
            'conductor_id' => $this->conductor->id,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['conductor_id']);

        $this->assertDatabaseCount('asignacion_turnos', 1);
    }

    public function test_cn_a_pe05_alta_rechaza_micro_duplicado_en_fecha_y_turno(): void
    {
        $this->crearAsignacion([
            'conductor_id' => $this->otroConductor->id,
            'micro_id' => $this->micro1->id,
        ]);

        Sanctum::actingAs($this->admin);

        $this->postJson('/api/asignacion-turnos', [
            'fecha' => now()->toDateString(),
            'turno_id' => $this->turnoActivo->id,
            'ruta_id' => $this->ruta->id,
            'micro_id' => $this->micro1->id,
            'conductor_id' => $this->conductor->id,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['micro_id']);

        $this->assertDatabaseCount('asignacion_turnos', 1);
    }

    public function test_cn_a_vl01_vl02_observaciones_acepta_1000_y_rechaza_1001_caracteres(): void
    {
        Sanctum::actingAs($this->admin);

        $payload = [
            'fecha' => now()->toDateString(),
            'turno_id' => $this->turnoActivo->id,
            'ruta_id' => $this->ruta->id,
            'micro_id' => $this->micro1->id,
            'conductor_id' => $this->conductor->id,
        ];

        $this->postJson('/api/asignacion-turnos', $payload + [
            'observaciones' => str_repeat('a', 1000),
        ])->assertCreated();

        $this->postJson('/api/asignacion-turnos', $payload + [
            'observaciones' => str_repeat('a', 1001),
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['observaciones']);

        $this->assertDatabaseCount('asignacion_turnos', 1);
    }

    public function test_cn_a_te01_conductor_inicia_su_turno_pendiente(): void
    {
        $asignacion = $this->crearAsignacion(['estado' => 'pendiente']);

        Sanctum::actingAs($this->conductorUser);

        $this->postJson("/api/mis/asignaciones/{$asignacion->id}/iniciar")
            ->assertOk()
            ->assertJsonPath('asignacion.estado', 'en_curso');

        $actualizada = $asignacion->fresh();

        $this->assertSame('en_curso', $actualizada->estado);
        $this->assertNotNull($actualizada->hora_salida);
    }

    public function test_cn_a_te02_conductor_finaliza_turno_y_guarda_observaciones(): void
    {
        $asignacion = $this->crearAsignacion(['estado' => 'en_curso']);

        Sanctum::actingAs($this->conductorUser);

        $this->postJson("/api/mis/asignaciones/{$asignacion->id}/finalizar", [
            'observaciones' => 'Cierre de turno',
        ])->assertOk()
            ->assertJsonPath('asignacion.estado', 'completado')
            ->assertJsonPath('asignacion.observaciones', 'Cierre de turno');

        $actualizada = $asignacion->fresh();

        $this->assertSame('completado', $actualizada->estado);
        $this->assertNotNull($actualizada->hora_llegada);
        $this->assertSame('Cierre de turno', $actualizada->observaciones);
    }

    public function test_cn_a_te05_rechaza_doble_inicio_y_conserva_hora_de_salida(): void
    {
        $asignacion = $this->crearAsignacion([
            'estado' => 'en_curso',
            'hora_salida' => '06:30:00',
        ]);

        Sanctum::actingAs($this->conductorUser);

        $this->postJson("/api/mis/asignaciones/{$asignacion->id}/iniciar")
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['estado']);

        $actualizada = $asignacion->fresh();

        $this->assertSame('en_curso', $actualizada->estado);
        $this->assertSame('06:30:00', $actualizada->hora_salida);
    }

    public function test_cn_a_te06_rechaza_finalizar_un_turno_pendiente(): void
    {
        $asignacion = $this->crearAsignacion(['estado' => 'pendiente']);

        Sanctum::actingAs($this->conductorUser);

        $this->postJson("/api/mis/asignaciones/{$asignacion->id}/finalizar")
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['estado']);

        $actualizada = $asignacion->fresh();

        $this->assertSame('pendiente', $actualizada->estado);
        $this->assertNull($actualizada->hora_llegada);
    }

    public function test_cn_a_te08_otro_conductor_no_puede_iniciar_la_asignacion(): void
    {
        $asignacion = $this->crearAsignacion(['estado' => 'pendiente']);

        Sanctum::actingAs($this->otroConductorUser);

        $this->postJson("/api/mis/asignaciones/{$asignacion->id}/iniciar")
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['conductor']);

        $this->assertSame('pendiente', $asignacion->fresh()->estado);
    }

    public function test_cn_a_te04_cancelacion_logica_oculta_la_asignacion(): void
    {
        $asignacion = $this->crearAsignacion(['estado' => 'pendiente']);

        Sanctum::actingAs($this->admin);

        $this->deleteJson("/api/asignacion-turnos/{$asignacion->id}")
            ->assertOk();

        $this->assertSame('cancelado', $asignacion->fresh()->estado);

        $this->getJson("/api/asignacion-turnos/{$asignacion->id}")
            ->assertNotFound();

        $ids = collect($this->getJson('/api/asignacion-turnos')->json('data'))->pluck('id');
        $this->assertFalse($ids->contains($asignacion->id));
    }

    // -----------------------------------------------------------------
    // Módulo B. Seguimiento GPS y control de recorrido
    // -----------------------------------------------------------------

    public function test_cn_b_pe08_punto_fuera_de_ruta_se_registra_en_control_general(): void
    {
        $asignacion = $this->crearAsignacion(['estado' => 'en_curso']);

        Sanctum::actingAs($this->conductorUser);

        $this->postJson("/api/mis/asignaciones/{$asignacion->id}/ubicaciones", [
            'fecha_hora_gps' => now()->subMinute()->toDateTimeString(),
            'latitud' => -17.78165300,
            'longitud' => -63.18200000,
            'velocidad' => 20.0,
        ])->assertCreated();

        $control = ControlRecorrido::where('asignacion_turno_id', $asignacion->id)
            ->whereNull('ruta_parada_id')
            ->first();

        $this->assertNotNull($control);
        $this->assertSame('en_curso', $control->estado);
        $this->assertSame('Seguimiento en ruta', $control->observacion);
        $this->assertSame(0, ControlRecorrido::where('asignacion_turno_id', $asignacion->id)->whereNotNull('ruta_parada_id')->count());
        $this->assertSame('en_curso', $asignacion->fresh()->estado);
    }

    public function test_cn_b_te02_punto_en_la_primera_parada_la_marca_cumplida(): void
    {
        $asignacion = $this->crearAsignacion(['estado' => 'en_curso']);

        Sanctum::actingAs($this->conductorUser);

        $this->postJson("/api/mis/asignaciones/{$asignacion->id}/ubicaciones", [
            'fecha_hora_gps' => now()->subMinute()->toDateTimeString(),
            'latitud' => -17.78300000,
            'longitud' => -63.18190590,
            'velocidad' => 12.0,
        ])->assertCreated();

        $control = ControlRecorrido::where('asignacion_turno_id', $asignacion->id)
            ->whereNotNull('ruta_parada_id')
            ->first();

        $this->assertNotNull($control);
        $this->assertSame('cumplido', $control->estado);
        $this->assertLessThanOrEqual(10.1, $control->distancia_metros);
        $this->assertSame('en_curso', $asignacion->fresh()->estado);
    }

    public function test_cn_b_vl07_vl08_vl09_geocerca_de_80_metros_es_inclusiva(): void
    {
        Sanctum::actingAs($this->conductorUser);

        $casos = [
            ['longitud' => -63.18124539, 'cumplida' => true, 'distancia' => 79.90],
            ['longitud' => -63.18124450, 'cumplida' => true, 'distancia' => 79.99],
            ['longitud' => -63.18124350, 'cumplida' => false, 'distancia' => 80.10],
        ];

        foreach ($casos as $caso) {
            $asignacion = $this->crearAsignacion(['estado' => 'en_curso']);

            $this->postJson("/api/mis/asignaciones/{$asignacion->id}/ubicaciones", [
                'fecha_hora_gps' => now()->subMinute()->toDateTimeString(),
                'latitud' => -17.78300000,
                'longitud' => $caso['longitud'],
                'velocidad' => 10.0,
            ])->assertCreated();

            $control = ControlRecorrido::where('asignacion_turno_id', $asignacion->id)
                ->whereNotNull('ruta_parada_id')
                ->first();

            if ($caso['cumplida']) {
                $this->assertNotNull($control);
                $this->assertSame('cumplido', $control->estado);
                $this->assertEqualsWithDelta($caso['distancia'], $control->distancia_metros, 0.1);
            } else {
                $this->assertNull($control);
                $this->assertTrue(ControlRecorrido::where('asignacion_turno_id', $asignacion->id)->whereNull('ruta_parada_id')->exists());
            }
        }
    }

    public function test_cn_b_pe01_a_pe04_rechaza_payload_gps_invalido(): void
    {
        $asignacion = $this->crearAsignacion(['estado' => 'en_curso']);

        Sanctum::actingAs($this->conductorUser);

        $base = [
            'fecha_hora_gps' => now()->subMinute()->toDateTimeString(),
            'latitud' => -17.78300000,
            'longitud' => -63.18200000,
            'velocidad' => 10.0,
        ];

        $this->postJson("/api/mis/asignaciones/{$asignacion->id}/ubicaciones", array_merge($base, ['latitud' => 91]))
            ->assertUnprocessable()->assertJsonValidationErrors(['latitud']);

        $this->postJson("/api/mis/asignaciones/{$asignacion->id}/ubicaciones", array_merge($base, ['longitud' => 181]))
            ->assertUnprocessable()->assertJsonValidationErrors(['longitud']);

        $this->postJson("/api/mis/asignaciones/{$asignacion->id}/ubicaciones", array_merge($base, ['velocidad' => -5]))
            ->assertUnprocessable()->assertJsonValidationErrors(['velocidad']);

        $this->postJson("/api/mis/asignaciones/{$asignacion->id}/ubicaciones", array_merge($base, [
            'fecha_hora_gps' => now()->addMinutes(3)->toDateTimeString(),
        ]))->assertUnprocessable()->assertJsonValidationErrors(['fecha_hora_gps']);

        $this->assertDatabaseCount('seguimiento_gps', 0);
    }

    public function test_cn_b_pe05_otro_conductor_no_puede_reportar_gps(): void
    {
        $asignacion = $this->crearAsignacion(['estado' => 'en_curso']);

        Sanctum::actingAs($this->otroConductorUser);

        $this->postJson("/api/mis/asignaciones/{$asignacion->id}/ubicaciones", [
            'fecha_hora_gps' => now()->subMinute()->toDateTimeString(),
            'latitud' => -17.78300000,
            'longitud' => -63.18200000,
            'velocidad' => 10.0,
        ])->assertForbidden();

        $this->assertDatabaseCount('seguimiento_gps', 0);
    }

    public function test_cn_b_pe09_punto_duplicado_no_genera_un_segundo_registro(): void
    {
        $asignacion = $this->crearAsignacion(['estado' => 'en_curso']);

        Sanctum::actingAs($this->conductorUser);

        $payload = [
            'fecha_hora_gps' => now()->subMinute()->toDateTimeString(),
            'latitud' => -17.78165300,
            'longitud' => -63.18200000,
            'velocidad' => 20.0,
        ];

        $this->postJson("/api/mis/asignaciones/{$asignacion->id}/ubicaciones", $payload)->assertCreated();
        $this->postJson("/api/mis/asignaciones/{$asignacion->id}/ubicaciones", $payload)->assertCreated();

        $this->assertSame(1, SeguimientoGps::query()->count());
    }

    public function test_cn_b_te08_sincronizacion_por_lote_reporta_guardados_y_duplicados(): void
    {
        $asignacion = $this->crearAsignacion(['estado' => 'en_curso']);

        Sanctum::actingAs($this->conductorUser);

        $punto = [
            'fecha_hora_gps' => now()->subMinute()->toDateTimeString(),
            'latitud' => -17.78165300,
            'longitud' => -63.18200000,
            'velocidad' => 20.0,
        ];

        $this->postJson('/api/mis/ubicaciones/sincronizar', [
            'asignacion_turno_id' => $asignacion->id,
            'ubicaciones' => [$punto, $punto],
        ])->assertOk()
            ->assertJsonPath('resultado.total_procesados', 2)
            ->assertJsonPath('resultado.guardados', 1)
            ->assertJsonPath('resultado.duplicados_omitidos', 1);

        $this->assertSame(1, SeguimientoGps::query()->count());
    }

    public function test_cn_b_vl11_lote_de_301_puntos_es_rechazado(): void
    {
        $asignacion = $this->crearAsignacion(['estado' => 'en_curso']);

        Sanctum::actingAs($this->conductorUser);

        $ubicaciones = collect(range(1, 301))->map(fn (int $i) => [
            'fecha_hora_gps' => now()->subMinutes(301 - $i)->toDateTimeString(),
            'latitud' => -17.78300000,
            'longitud' => -63.18200000,
            'velocidad' => 10.0,
        ])->all();

        $this->postJson('/api/mis/ubicaciones/sincronizar', [
            'asignacion_turno_id' => $asignacion->id,
            'ubicaciones' => $ubicaciones,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['ubicaciones']);

        $this->assertDatabaseCount('seguimiento_gps', 0);
    }

    public function test_cn_b_te06_asignacion_completada_no_acepta_nuevos_puntos(): void
    {
        $asignacion = $this->crearAsignacion(['estado' => 'completado']);

        Sanctum::actingAs($this->conductorUser);

        $this->postJson("/api/mis/asignaciones/{$asignacion->id}/ubicaciones", [
            'fecha_hora_gps' => now()->subMinute()->toDateTimeString(),
            'latitud' => -17.78300000,
            'longitud' => -63.18200000,
            'velocidad' => 10.0,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['asignacion_turno_id']);

        $this->assertDatabaseCount('seguimiento_gps', 0);
    }

    public function test_cn_b_te07_asignacion_cancelada_no_esta_disponible_para_gps(): void
    {
        $asignacion = $this->crearAsignacion(['estado' => 'cancelado']);

        Sanctum::actingAs($this->conductorUser);

        $this->postJson("/api/mis/asignaciones/{$asignacion->id}/ubicaciones", [
            'fecha_hora_gps' => now()->subMinute()->toDateTimeString(),
            'latitud' => -17.78300000,
            'longitud' => -63.18200000,
            'velocidad' => 10.0,
        ])->assertNotFound();

        $this->assertDatabaseCount('seguimiento_gps', 0);
    }

    public function test_cn_b_te04_secuencia_ida_vuelta_y_cierre_automatico(): void
    {
        $ruta = Ruta::create(['nombre' => 'Ruta Circuito 61', 'estado' => 'activo']);

        $paradas = collect([
            ['nombre' => 'I1', 'latitud' => -17.78300000, 'longitud' => -63.18200000, 'sentido' => 'Ida', 'orden' => 1],
            ['nombre' => 'I2', 'latitud' => -17.78400000, 'longitud' => -63.18300000, 'sentido' => 'Ida', 'orden' => 2],
            ['nombre' => 'V1', 'latitud' => -17.78500000, 'longitud' => -63.18400000, 'sentido' => 'Vuelta', 'orden' => 1],
            ['nombre' => 'V2', 'latitud' => -17.78600000, 'longitud' => -63.18500000, 'sentido' => 'Vuelta', 'orden' => 2],
        ])->map(function (array $datos) use ($ruta) {
            $parada = Parada::create($datos);

            return RutaParada::create([
                'ruta_id' => $ruta->id,
                'parada_id' => $parada->id,
                'orden' => $datos['orden'],
                'sentido' => $datos['sentido'],
                'estado' => 'activo',
            ]);
        });

        $asignacion = $this->crearAsignacion([
            'ruta_id' => $ruta->id,
            'estado' => 'en_curso',
        ]);

        Sanctum::actingAs($this->conductorUser);

        foreach ($paradas as $indice => $rutaParada) {
            $parada = $rutaParada->parada;

            $this->postJson("/api/mis/asignaciones/{$asignacion->id}/ubicaciones", [
                'fecha_hora_gps' => now()->subMinutes(4 - $indice)->toDateTimeString(),
                'latitud' => $parada->latitud,
                'longitud' => $parada->longitud,
                'velocidad' => 15.0,
            ])->assertCreated();

            $control = ControlRecorrido::where('asignacion_turno_id', $asignacion->id)
                ->where('ruta_parada_id', $rutaParada->id)
                ->first();

            $this->assertNotNull($control);
            $this->assertSame('cumplido', $control->estado);

            $estadoEsperado = $indice === $paradas->count() - 1 ? 'completado' : 'en_curso';
            $this->assertSame($estadoEsperado, $asignacion->fresh()->estado);
        }

        $this->assertNotNull($asignacion->fresh()->hora_llegada);
    }

    public function test_cn_b_c02_consulta_de_recorrido_devuelve_ultima_posicion_y_paradas_cumplidas(): void
    {
        $asignacion = $this->crearAsignacion(['estado' => 'en_curso']);

        Sanctum::actingAs($this->conductorUser);

        $this->postJson("/api/mis/asignaciones/{$asignacion->id}/ubicaciones", [
            'fecha_hora_gps' => now()->subMinute()->toDateTimeString(),
            'latitud' => -17.78300000,
            'longitud' => -63.18200000,
            'velocidad' => 10.0,
        ])->assertCreated();

        $this->getJson("/api/mis/asignaciones/{$asignacion->id}/recorrido")
            ->assertOk()
            ->assertJsonPath('asignacion.id', $asignacion->id)
            ->assertJsonCount(1, 'paradas_cumplidas')
            ->assertJsonPath('paradas_cumplidas.0.estado', 'cumplido');

        $this->assertNotNull(SeguimientoGps::query()->first());
    }

    public function test_cn_b_c01_control_de_recorrido_web_muestra_solo_asignaciones_filtradas(): void
    {
        $visible = $this->crearAsignacion(['estado' => 'en_curso']);
        $cancelada = $this->crearAsignacion([
            'conductor_id' => $this->otroConductor->id,
            'micro_id' => $this->micro2->id,
            'estado' => 'cancelado',
        ]);

        $this->actingAs($this->admin);

        $response = $this->get(route('control-recorrido.index', [
            'fecha' => now()->toDateString(),
            'ruta_id' => $this->ruta->id,
            'estado' => 'en_curso',
        ]));

        $response->assertOk()
            ->assertSee('Control de Recorrido')
            ->assertSee($this->ruta->nombre);

        $codigoVisible = 'AT-'.str_pad((string) $visible->id, 3, '0', STR_PAD_LEFT);
        $codigoCancelada = 'AT-'.str_pad((string) $cancelada->id, 3, '0', STR_PAD_LEFT);

        $response->assertSee($codigoVisible);
        $response->assertDontSee($codigoCancelada);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    protected function crearAsignacion(array $overrides = []): AsignacionTurno
    {
        return AsignacionTurno::create(array_merge([
            'fecha' => now()->toDateString(),
            'turno_id' => $this->turnoActivo->id,
            'ruta_id' => $this->ruta->id,
            'micro_id' => $this->micro1->id,
            'conductor_id' => $this->conductor->id,
            'estado' => 'pendiente',
        ], $overrides));
    }
}
