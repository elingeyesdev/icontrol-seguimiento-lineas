<?php

namespace Tests\Feature;

use App\Models\AsignacionTurno;
use App\Models\Conductor;
use App\Models\Micro;
use App\Models\Propietario;
use App\Models\Ruta;
use App\Models\Turno;
use App\Models\User;
use App\Services\AsignacionTurnoService;
use Database\Seeders\RoleSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Integridad de las asignaciones de turno (plan de cambio, sección 9).
 *
 * Verifica que desactivar un catálogo no deje asignaciones operativas
 * apuntando a un recurso fuera de servicio, y que las asignaciones
 * históricas no se destruyan.
 */
class IntegridadAsignacionesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Propietario $propietario;

    private int $secuencia = 0;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->propietario = Propietario::create([
            'nombre' => 'Propietario',
            'apellido' => 'Prueba',
            'correo' => 'propietario@example.com',
            'ci' => '9000001',
            'estado' => 'activo',
        ]);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function entidades(): array
    {
        return [
            'conductor' => ['conductor'],
            'micro' => ['micro'],
            'ruta' => ['ruta'],
            'turno' => ['turno'],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function estadosOperativos(): array
    {
        return [
            'pendiente' => ['pendiente'],
            'en curso' => ['en_curso'],
            'retrasado' => ['retrasado'],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function estadosHistoricos(): array
    {
        return [
            'completado' => ['completado'],
            'cancelado' => ['cancelado'],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function recursosDelCatalogo(): array
    {
        return [
            'conductor' => ['conductor'],
            'micro' => ['micro'],
            'ruta' => ['ruta'],
        ];
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function entidadesConEstadosOperativos(): array
    {
        return self::combinar(self::entidades(), self::estadosOperativos());
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function entidadesConEstadosHistoricos(): array
    {
        return self::combinar(self::entidades(), self::estadosHistoricos());
    }

    /**
     * @param  array<string, array{string}>  $entidades
     * @param  array<string, array{string}>  $estados
     * @return array<string, array{string, string}>
     */
    private static function combinar(array $entidades, array $estados): array
    {
        $casos = [];

        foreach ($entidades as $nombreEntidad => [$entidad]) {
            foreach ($estados as $nombreEstado => [$estado]) {
                $casos["{$nombreEntidad} {$nombreEstado}"] = [$entidad, $estado];
            }
        }

        return $casos;
    }

    #[DataProvider('entidadesConEstadosOperativos')]
    public function test_desactivacion_web_se_bloquea_con_asignacion_operativa(string $tipo, string $estado): void
    {
        $entidad = $this->crearEntidad($tipo);
        $asignacion = $this->crearAsignacion($estado, [$this->columnaDe($tipo) => $entidad->getKey()]);

        $this->actingAs($this->admin)
            ->delete(route("{$tipo}.destroy", $entidad->getKey()))
            ->assertRedirect()
            ->assertSessionHas('error', fn (string $mensaje) => str_contains($mensaje, 'asignación'));

        $this->assertSame('activo', $entidad->fresh()->estado);
        $this->assertSame($estado, $asignacion->fresh()->estado);
    }

    #[DataProvider('entidadesConEstadosHistoricos')]
    public function test_desactivacion_web_se_permite_con_historial(string $tipo, string $estado): void
    {
        $entidad = $this->crearEntidad($tipo);
        $asignacion = $this->crearAsignacion($estado, [$this->columnaDe($tipo) => $entidad->getKey()]);

        $this->actingAs($this->admin)
            ->delete(route("{$tipo}.destroy", $entidad->getKey()))
            ->assertRedirect();

        $this->assertSame('inactivo', $entidad->fresh()->estado);
        $this->assertSame($estado, $asignacion->fresh()->estado);
        $this->assertDatabaseHas('asignacion_turnos', ['id' => $asignacion->id]);
    }

    #[DataProvider('entidades')]
    public function test_actualizacion_por_api_no_puede_desactivar_con_asignacion_operativa(string $tipo): void
    {
        $entidad = $this->crearEntidad($tipo);
        $this->crearAsignacion('en_curso', [$this->columnaDe($tipo) => $entidad->getKey()]);

        $this->actingAs($this->admin, 'sanctum')
            ->putJson($this->rutaApi($tipo, 'update', $entidad->getKey()), $this->payloadDesactivacion($entidad, $tipo))
            ->assertStatus(422)
            ->assertJsonValidationErrors('estado');

        $this->assertSame('activo', $entidad->fresh()->estado);
    }

    #[DataProvider('entidades')]
    public function test_api_destroy_bloquea_y_no_deja_cambios_parciales(string $tipo): void
    {
        $entidad = $this->crearEntidad($tipo);
        $asignacion = $this->crearAsignacion('pendiente', [$this->columnaDe($tipo) => $entidad->getKey()]);

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson($this->rutaApi($tipo, 'destroy', $entidad->getKey()))
            ->assertStatus(422)
            ->assertJsonValidationErrors('estado');

        $this->assertSame('activo', $entidad->fresh()->estado);
        $this->assertSame('pendiente', $asignacion->fresh()->estado);
    }

    #[DataProvider('entidades')]
    public function test_api_destroy_permite_desactivar_con_asignacion_completada(string $tipo): void
    {
        $entidad = $this->crearEntidad($tipo);
        $asignacion = $this->crearAsignacion('completado', [$this->columnaDe($tipo) => $entidad->getKey()]);

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson($this->rutaApi($tipo, 'destroy', $entidad->getKey()))
            ->assertOk();

        $this->assertSame('inactivo', $entidad->fresh()->estado);
        $this->assertDatabaseHas('asignacion_turnos', ['id' => $asignacion->id]);
    }

    #[DataProvider('recursosDelCatalogo')]
    public function test_una_asignacion_nueva_no_acepta_recursos_inactivos(string $tipo): void
    {
        $entidad = $this->crearEntidad($tipo);
        $entidad->update(['estado' => 'inactivo']);

        $datos = $this->datosDeAsignacion([$this->columnaDe($tipo) => $entidad->getKey()]);

        $this->expectException(ValidationException::class);

        app(AsignacionTurnoService::class)->crear($datos);
    }

    public function test_una_asignacion_nueva_con_recursos_activos_se_crea(): void
    {
        $datos = $this->datosDeAsignacion();

        $asignacion = app(AsignacionTurnoService::class)->crear($datos);

        $this->assertSame('pendiente', $asignacion->estado);
        $this->assertDatabaseHas('asignacion_turnos', ['id' => $asignacion->id]);
    }

    public function test_la_eliminacion_fisica_no_arrastra_historial(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            $this->markTestSkipped('La restricción de claves foráneas solo se valida en MySQL/MariaDB; SQLite no permite alterarlas.');
        }

        $conductor = $this->crearEntidad('conductor');
        $asignacion = $this->crearAsignacion('completado', ['conductor_id' => $conductor->getKey()]);

        try {
            $conductor->delete();
        } catch (QueryException) {
            // Comportamiento esperado: RESTRICT impide el borrado físico.
        }

        $this->assertDatabaseHas('asignacion_turnos', ['id' => $asignacion->id]);
        $this->assertDatabaseHas('conductor', ['id' => $conductor->getKey()]);
    }

    private function crearEntidad(string $tipo): Model
    {
        $numero = ++$this->secuencia;

        return match ($tipo) {
            'conductor' => Conductor::create([
                'nombre' => 'Conductor',
                'apellido' => 'Prueba '.$numero,
                'telefono' => '70000000',
                'correo' => "conductor{$numero}@example.com",
                'ci' => "80000{$numero}",
                'estado' => 'activo',
            ]),
            'micro' => Micro::create([
                'propietario_id' => $this->propietario->id,
                'placa' => "1234-ABC{$numero}",
                'modelo' => 'Sprinter',
                'marca' => 'Mercedes',
                'capacidad_pasajeros' => 30,
                'estado' => 'activo',
            ]),
            'ruta' => Ruta::create([
                'nombre' => 'Ruta Troncal '.$numero,
                'estado' => 'activo',
            ]),
            'turno' => Turno::create([
                'nombre' => 'tarde',
                'hora_inicio' => '14:00:00',
                'hora_fin' => '22:00:00',
                'estado' => 'activo',
            ]),
        };
    }

    /**
     * @param  array<string, mixed>  $sobrescribir
     */
    private function crearAsignacion(string $estado, array $sobrescribir = []): AsignacionTurno
    {
        return AsignacionTurno::create(
            $this->datosDeAsignacion($sobrescribir) + ['estado' => $estado]
        );
    }

    /**
     * @param  array<string, mixed>  $sobrescribir
     * @return array<string, mixed>
     */
    private function datosDeAsignacion(array $sobrescribir = []): array
    {
        return array_merge([
            'fecha' => now()->toDateString(),
            'turno_id' => $this->crearEntidad('turno')->getKey(),
            'ruta_id' => $this->crearEntidad('ruta')->getKey(),
            'micro_id' => $this->crearEntidad('micro')->getKey(),
            'conductor_id' => $this->crearEntidad('conductor')->getKey(),
        ], $sobrescribir);
    }

    private function columnaDe(string $tipo): string
    {
        return $tipo === 'conductor' ? 'conductor_id' : "{$tipo}_id";
    }

    private function rutaApi(string $tipo, string $accion, int $id): string
    {
        $recurso = match ($tipo) {
            'conductor' => 'conductores',
            'micro' => 'micros',
            'ruta' => 'rutas',
            'turno' => 'turnos',
        };

        return route("{$recurso}.{$accion}", $id);
    }

    /**
     * @return array<string, mixed>
     */
    private function payloadDesactivacion(Model $entidad, string $tipo): array
    {
        return match ($tipo) {
            'conductor' => [
                'nombre' => $entidad->nombre,
                'apellido' => $entidad->apellido,
                'telefono' => $entidad->telefono,
                'correo' => $entidad->correo,
                'ci' => $entidad->ci,
                'estado' => 'inactivo',
            ],
            'micro' => ['estado' => 'inactivo'],
            'ruta' => ['nombre' => $entidad->nombre, 'estado' => 'inactivo'],
            'turno' => ['nombre' => $entidad->nombre, 'estado' => 'inactivo'],
        };
    }
}
