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
use App\Services\AsignacionTurnoService;
use App\Services\SeguimientoGpsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ControlRecorridoTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected AsignacionTurno $asignacion;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $this->admin = User::factory()->create([
            'ci' => '1234567',
        ]);
        $this->admin->assignRole($role);

        $turno = Turno::create([
            'nombre' => 'mañana',
            'hora_inicio' => '06:00:00',
            'hora_fin' => '14:00:00',
            'dias_semana' => ['lunes', 'martes', 'miercoles'],
            'estado' => 'activo',
        ]);

        $ruta = Ruta::create([
            'nombre' => 'Línea 61 Troncal',
            'estado' => 'activo',
        ]);

        $parada1 = Parada::create([
            'nombre' => 'Parada 1',
            'latitud' => -17.7830,
            'longitud' => -63.1820,
            'estado' => 'activo',
        ]);

        RutaParada::create([
            'ruta_id' => $ruta->id,
            'parada_id' => $parada1->id,
            'orden' => 1,
            'sentido' => 'Ida',
            'estado' => 'activo',
        ]);

        $propietario = Propietario::create([
            'nombre' => 'Carlos',
            'apellido' => 'Suarez',
            'telefono' => '71111111',
            'estado' => 'activo',
        ]);

        $micro = Micro::create([
            'placa' => '2020-ABC',
            'modelo' => 'Coaster',
            'marca' => 'Toyota',
            'chasis' => 'CHASIS-12345',
            'anio_fabricacion' => 2018,
            'capacidad_pasajeros' => 30,
            'estado' => 'activo',
            'propietario_id' => $propietario->id,
        ]);

        $conductor = Conductor::create([
            'nombre' => 'Juan',
            'apellido' => 'Perez',
            'telefono' => '70000000',
            'licencia' => 'CAT-C-1234',
            'categoria_licencia' => 'C',
            'estado' => 'activo',
        ]);

        $this->asignacion = AsignacionTurno::create([
            'fecha' => now()->toDateString(),
            'turno_id' => $turno->id,
            'ruta_id' => $ruta->id,
            'micro_id' => $micro->id,
            'conductor_id' => $conductor->id,
            'hora_salida' => '06:30:00',
            'estado' => 'en_curso',
        ]);
    }

    public function test_index_screen_can_be_rendered(): void
    {
        $response = $this->actingAs($this->admin)->get(route('control-recorrido.index'));
        $response->assertOk();
        $response->assertSee('Control de Recorrido');
        $response->assertSee('2020-ABC');
    }

    public function test_index_can_search_assignments_by_code_conductor_micro_and_route(): void
    {
        $codigo = 'AT-'.str_pad($this->asignacion->id, 3, '0', STR_PAD_LEFT);

        foreach ([$codigo, 'Juan', '2020-ABC', 'Línea 61 Troncal'] as $termino) {
            $this->actingAs($this->admin)
                ->get(route('control-recorrido.index', ['buscar' => $termino]))
                ->assertOk()
                ->assertSee($codigo);
        }

        $this->actingAs($this->admin)
            ->get(route('control-recorrido.index', ['buscar' => 'sin-coincidencias']))
            ->assertOk()
            ->assertSee('Sin asignaciones');
    }

    public function test_show_screen_can_be_rendered(): void
    {
        $response = $this->actingAs($this->admin)->get(route('control-recorrido.show', $this->asignacion->id));
        $response->assertOk();
        $response->assertSee('Detalle de Recorrido');
        $response->assertSee('Parada 1');
        $response->assertDontSee('Historial completo');
        $response->assertDontSee('Mapa ampliado');
        $response->assertDontSee('mapDetalle', false);
    }

    public function test_historial_and_mapa_screens_are_no_longer_available(): void
    {
        $this->actingAs($this->admin)
            ->get("/control-recorrido/{$this->asignacion->id}/historial")
            ->assertNotFound();

        $this->actingAs($this->admin)
            ->get("/control-recorrido/{$this->asignacion->id}/mapa")
            ->assertNotFound();
    }

    public function test_admin_fiscalizador_propietario_can_access_monitoreo(): void
    {
        // 1. Admin
        $response = $this->actingAs($this->admin)->get(route('monitoreo.index'));
        $response->assertOk();

        // 2. Fiscalizador
        $roleFisc = Role::firstOrCreate(['name' => 'fiscalizador', 'guard_name' => 'web']);
        $fisc = User::factory()->create(['ci' => '7777777']);
        $fisc->assignRole($roleFisc);
        $this->actingAs($fisc)->get(route('monitoreo.index'))->assertOk();

        // 3. Propietario
        $roleProp = Role::firstOrCreate(['name' => 'propietario', 'guard_name' => 'web']);
        $prop = User::factory()->create(['ci' => '8888888']);
        $prop->assignRole($roleProp);
        $this->actingAs($prop)->get(route('monitoreo.index'))->assertOk();
    }

    public function test_iniciar_turno_planifica_todas_las_paradas_de_la_ruta(): void
    {
        $parada2 = Parada::create([
            'nombre' => 'Parada 2',
            'latitud' => -17.7840,
            'longitud' => -63.1830,
            'estado' => 'activo',
        ]);

        RutaParada::create([
            'ruta_id' => $this->asignacion->ruta_id,
            'parada_id' => $parada2->id,
            'orden' => 2,
            'sentido' => 'Ida',
            'estado' => 'activo',
        ]);

        $this->asignacion->update(['estado' => 'pendiente']);

        app(AsignacionTurnoService::class)->iniciar($this->asignacion);

        $controles = ControlRecorrido::where('asignacion_turno_id', $this->asignacion->id)->get();

        $this->assertCount(2, $controles);
        $this->assertTrue($controles->every(fn (ControlRecorrido $control) => $control->estado === 'pendiente'));
        $this->assertTrue($controles->every(fn (ControlRecorrido $control) => $control->fecha_hora === null));
    }

    public function test_evaluar_punto_omite_paradas_intermedias_y_cierra_el_recorrido(): void
    {
        $parada2 = Parada::create([
            'nombre' => 'Parada 2',
            'latitud' => -17.7840,
            'longitud' => -63.1830,
            'estado' => 'activo',
        ]);

        $parada3 = Parada::create([
            'nombre' => 'Parada 3',
            'latitud' => -17.7850,
            'longitud' => -63.1840,
            'estado' => 'activo',
        ]);

        RutaParada::create([
            'ruta_id' => $this->asignacion->ruta_id,
            'parada_id' => $parada2->id,
            'orden' => 2,
            'sentido' => 'Ida',
            'estado' => 'activo',
        ]);

        RutaParada::create([
            'ruta_id' => $this->asignacion->ruta_id,
            'parada_id' => $parada3->id,
            'orden' => 3,
            'sentido' => 'Ida',
            'estado' => 'activo',
        ]);

        $resultado = app(SeguimientoGpsService::class)->registrarPunto($this->asignacion, [
            'fecha_hora_gps' => now()->subMinute()->toDateTimeString(),
            'latitud' => -17.7850,
            'longitud' => -63.1840,
            'velocidad' => 20.0,
        ]);

        $this->assertSame('cumplido', $resultado['control_recorrido']->estado);
        $this->assertSame('completado', $this->asignacion->fresh()->estado);

        $estados = ControlRecorrido::where('asignacion_turno_id', $this->asignacion->id)
            ->whereNotNull('ruta_parada_id')
            ->pluck('estado', 'ruta_parada_id');

        $this->assertSame('omitido', $estados[RutaParada::where('parada_id', $parada2->id)->first()->id]);
        $this->assertSame('cumplido', $estados[RutaParada::where('parada_id', $parada3->id)->first()->id]);
    }

    public function test_monitoreo_solo_expone_datos_de_posicion(): void
    {
        $response = $this->actingAs($this->admin)->getJson(route('monitoreo.posiciones'));

        $response->assertOk();
        $response->assertJsonPath('total_activas', 1);

        $unidad = $response->json('unidades.0');

        $this->assertArrayHasKey('latitud', $unidad);
        $this->assertArrayHasKey('ultima_actualizacion', $unidad);
        $this->assertArrayNotHasKey('paradas', $unidad);
        $this->assertArrayNotHasKey('total_paradas', $unidad);
        $this->assertArrayNotHasKey('sentido', $unidad);
    }

    public function test_control_recorrido_relations_work_via_has_through(): void
    {
        $control = ControlRecorrido::create([
            'asignacion_turno_id' => $this->asignacion->id,
            'fecha_hora_inicio' => now(),
            'fecha_hora' => now(),
            'estado' => 'en_curso',
        ]);

        $gps = SeguimientoGps::create([
            'control_recorrido_id' => $control->id,
            'fecha_hora_gps' => now(),
            'latitud' => -17.7830,
            'longitud' => -63.1820,
            'velocidad' => 15.0,
        ]);

        $this->assertNotNull($control->asignacionTurno);
        $this->assertSame($this->asignacion->id, $control->asignacionTurno->id);
        $this->assertSame($this->asignacion->id, $gps->asignacionTurno->id);
        $this->assertTrue($control->seguimientosGps->contains($gps));
        $this->assertTrue($this->asignacion->seguimientosGps->contains($gps));
        $this->assertTrue($this->asignacion->controlesRecorrido->contains($control));
    }
}
