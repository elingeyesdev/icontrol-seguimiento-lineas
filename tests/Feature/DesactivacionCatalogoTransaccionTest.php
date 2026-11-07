<?php

namespace Tests\Feature;

use App\Models\AsignacionTurno;
use App\Models\Conductor;
use App\Models\Micro;
use App\Models\Propietario;
use App\Models\Ruta;
use App\Models\Turno;
use App\Services\IntegridadAsignacionesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DesactivacionCatalogoTransaccionTest extends TestCase
{
    use RefreshDatabase;

    private Ruta $ruta;

    private Turno $turno;

    private Conductor $conductor;

    private Micro $micro;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ruta = Ruta::create([
            'nombre' => 'Ruta Desactivación Test',
            'estado' => 'activo',
        ]);

        $this->turno = Turno::create([
            'nombre' => 'mañana',
            'hora_inicio' => '06:00:00',
            'hora_fin' => '14:00:00',
            'estado' => 'activo',
        ]);

        $this->conductor = Conductor::create([
            'nombre' => 'Conductor',
            'apellido' => 'Prueba',
            'licencia' => 'LIC-DE-01',
            'estado' => 'activo',
        ]);

        $propietario = Propietario::create([
            'nombre' => 'Propietario',
            'apellido' => 'Prueba',
            'ci' => '9000001',
            'estado' => 'activo',
        ]);

        $this->micro = Micro::create([
            'propietario_id' => $propietario->id,
            'placa' => 'DE01-TEST',
            'marca' => 'Toyota',
            'modelo' => 'Coaster',
            'capacidad_pasajeros' => 30,
            'estado' => 'activo',
        ]);
    }

    public function test_de01_activo_sin_asignaciones_pasa_a_inactivo(): void
    {
        $resultado = $this->service()->desactivar($this->ruta);

        $this->assertSame('inactivo', $resultado->estado);
        $this->assertSame('inactivo', $this->ruta->fresh()->estado);
    }

    public function test_de02_activo_con_asignacion_pendiente_se_bloquea(): void
    {
        $this->crearAsignacion('pendiente');

        $this->expectException(ValidationException::class);

        $this->service()->desactivar($this->ruta);
    }

    public function test_de03_activo_con_asignacion_completada_pasa_a_inactivo(): void
    {
        $asignacion = $this->crearAsignacion('completado');

        $resultado = $this->service()->desactivar($this->ruta);

        $this->assertSame('inactivo', $resultado->estado);
        $this->assertSame('completado', $asignacion->fresh()->estado);
    }

    public function test_de04_inactivo_permanece_inactivo(): void
    {
        $this->ruta->update(['estado' => 'inactivo']);

        $resultado = $this->service()->desactivar($this->ruta);

        $this->assertSame('inactivo', $resultado->estado);
        $this->assertSame('inactivo', $this->ruta->fresh()->estado);
    }

    public function test_de05_activo_con_asignacion_retrasada_se_bloquea(): void
    {
        $this->crearAsignacion('retrasado');

        $this->expectException(ValidationException::class);

        $this->service()->desactivar($this->ruta);
    }

    private function service(): IntegridadAsignacionesService
    {
        return app(IntegridadAsignacionesService::class);
    }

    private function crearAsignacion(string $estado): AsignacionTurno
    {
        return AsignacionTurno::create([
            'fecha' => now()->toDateString(),
            'turno_id' => $this->turno->id,
            'ruta_id' => $this->ruta->id,
            'micro_id' => $this->micro->id,
            'conductor_id' => $this->conductor->id,
            'estado' => $estado,
        ]);
    }
}
