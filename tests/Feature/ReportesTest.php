<?php

namespace Tests\Feature;

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

class ReportesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected AsignacionTurno $asignacion;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $this->admin = User::factory()->create(['ci' => '1234567']);
        $this->admin->assignRole($role);

        $propietario = Propietario::create([
            'nombre' => 'Carlos',
            'apellido' => 'Suarez',
            'estado' => 'activo',
        ]);

        $interno = Interno::create([
            'numero_interno' => 'INT-45',
            'fecha_ingreso' => now()->toDateString(),
            'estado' => 'asignado',
        ]);

        $micro = Micro::create([
            'propietario_id' => $propietario->id,
            'interno_id' => $interno->id,
            'placa' => '2020-ABC',
            'marca' => 'Toyota',
            'modelo' => 'Coaster',
            'capacidad_pasajeros' => 30,
            'estado' => 'activo',
        ]);

        $conductor = Conductor::create([
            'nombre' => 'Juan',
            'apellido' => 'Perez',
            'estado' => 'activo',
        ]);

        $turno = Turno::create([
            'nombre' => 'mañana',
            'hora_inicio' => '06:00:00',
            'hora_fin' => '14:00:00',
            'estado' => 'activo',
        ]);

        $ruta = Ruta::create([
            'nombre' => 'Línea 61 Troncal',
            'estado' => 'activo',
        ]);

        $paradaCumplida = Parada::create([
            'nombre' => 'Parada Cumplida',
            'latitud' => -17.7830,
            'longitud' => -63.1820,
            'estado' => 'activo',
        ]);

        $paradaOmitida = Parada::create([
            'nombre' => 'Parada Omitida',
            'latitud' => -17.7900,
            'longitud' => -63.1900,
            'estado' => 'activo',
        ]);

        $rutaParadaCumplida = RutaParada::create([
            'ruta_id' => $ruta->id,
            'parada_id' => $paradaCumplida->id,
            'orden' => 1,
            'sentido' => 'Ida',
            'estado' => 'activo',
        ]);

        RutaParada::create([
            'ruta_id' => $ruta->id,
            'parada_id' => $paradaOmitida->id,
            'orden' => 2,
            'sentido' => 'Ida',
            'estado' => 'activo',
        ]);

        $this->asignacion = AsignacionTurno::create([
            'fecha' => now()->toDateString(),
            'turno_id' => $turno->id,
            'ruta_id' => $ruta->id,
            'micro_id' => $micro->id,
            'conductor_id' => $conductor->id,
            'hora_salida' => '06:30:00',
            'estado' => 'completado',
        ]);

        ControlRecorrido::create([
            'asignacion_turno_id' => $this->asignacion->id,
            'ruta_parada_id' => $rutaParadaCumplida->id,
            'fecha_hora' => now(),
            'estado' => 'cumplido',
        ]);
    }

    public function test_panel_de_reportes_can_be_rendered(): void
    {
        $response = $this->actingAs($this->admin)->get(route('reportes.index'));

        $response->assertOk();
        $response->assertSee('Módulo de Reportes');
        $response->assertSee('Asignaciones de Turnos por Conductor');
        $response->assertSee('Micros por Propietario');
        $response->assertSee('Paradas Omitidas');
    }

    public function test_reporte_asignaciones_por_conductor_can_be_rendered(): void
    {
        $response = $this->actingAs($this->admin)->get(route('reportes.show', 'asignaciones-conductor'));

        $response->assertOk();
        $response->assertSee('Juan Perez');
        $response->assertSee('2020-ABC');
        $response->assertSee('Línea 61 Troncal');
    }

    public function test_reporte_asignaciones_filtra_por_mes_y_anio(): void
    {
        $mesAnterior = now()->copy()->subMonthNoOverflow();

        $response = $this->actingAs($this->admin)->get(route('reportes.show', [
            'reporte' => 'asignaciones-conductor',
            'mes' => $mesAnterior->month,
            'anio' => $mesAnterior->year,
        ]));

        $response->assertOk();
        $response->assertDontSee('2020-ABC');
    }

    public function test_reporte_micros_por_propietario_can_be_rendered(): void
    {
        $response = $this->actingAs($this->admin)->get(route('reportes.show', 'micros-propietario'));

        $response->assertOk();
        $response->assertSee('2020-ABC');
        $response->assertSee('Carlos Suarez');
        $response->assertSee('INT-45');
    }

    public function test_reporte_paradas_omitidas_can_be_rendered(): void
    {
        $response = $this->actingAs($this->admin)->get(route('reportes.show', 'paradas-omitidas'));

        $response->assertOk();
        $response->assertSee('Parada Omitida');
        $response->assertDontSee('Parada Cumplida');
    }

    public function test_reporte_pdf_puede_exportarse(): void
    {
        $response = $this->actingAs($this->admin)->get(route('reportes.pdf', 'asignaciones-conductor'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_reporte_excel_puede_exportarse(): void
    {
        $response = $this->actingAs($this->admin)->get(route('reportes.excel', 'asignaciones-conductor'));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
    }

    public function test_reporte_desconocido_devuelve_404(): void
    {
        $this->actingAs($this->admin)->get(route('reportes.show', 'inexistente'))->assertNotFound();
    }
}
