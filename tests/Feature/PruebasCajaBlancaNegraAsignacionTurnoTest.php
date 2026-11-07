<?php

namespace Tests\Feature;

use App\Models\AsignacionTurno;
use App\Models\Conductor;
use App\Models\Micro;
use App\Models\Propietario;
use App\Models\Ruta;
use App\Models\Turno;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pruebas de caja blanca y caja negra para la operación transaccional
 * "Asignación de turno", procedimiento Store.
 *
 * Caja blanca: cobertura de instrucciones, decisiones, condiciones y caminos.
 * Caja negra: partición de equivalencia, valores límite, tabla de decisión
 * y transición de estados.
 *
 * Cada método representa un caso de prueba del informe y debe pasar para
 * confirmar que el resultado obtenido coincide con el esperado.
 */
class PruebasCajaBlancaNegraAsignacionTurnoTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Propietario $propietario;

    private Turno $turno;

    private Ruta $ruta;

    private Micro $micro;

    private Conductor $conductor;

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
            'telefono' => '70000000',
            'correo' => 'propietario.pruebas@linea61.test',
            'ci' => '9000001',
            'estado' => 'activo',
        ]);

        $this->turno = Turno::create([
            'nombre' => 'tarde',
            'hora_inicio' => '13:00:00',
            'hora_fin' => '21:00:00',
            'descripcion' => 'Turno de prueba',
            'estado' => 'activo',
        ]);

        $this->ruta = $this->crearRuta();
        $this->micro = $this->crearMicro();
        $this->conductor = $this->crearConductor();
    }

    // ─────────────────────────────────────────────────────────────────
    // CAJA BLANCA · MÉTODO 1: COBERTURA DE INSTRUCCIONES (CB-I)
    // ─────────────────────────────────────────────────────────────────

    public function test_cb_i01_store_ejecuta_todas_las_instrucciones_del_camino_exitoso(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload());

        $response->assertRedirect(route('asignacion-turno.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseCount('asignacion_turnos', 1);
        $this->assertDatabaseHas('asignacion_turnos', [
            'turno_id' => $this->turno->id,
            'ruta_id' => $this->ruta->id,
            'micro_id' => $this->micro->id,
            'conductor_id' => $this->conductor->id,
            'estado' => 'pendiente',
            'hora_salida' => null,
            'hora_llegada' => null,
        ]);
    }

    public function test_cb_i02_store_turno_inactivo_ejecuta_la_instruccion_de_rechazo(): void
    {
        $this->turno->update(['estado' => 'inactivo']);

        $response = $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload());

        $response->assertSessionHasErrors('turno_id');
        $this->assertDatabaseCount('asignacion_turnos', 0);
    }

    public function test_cb_i03_store_micro_inactivo_ejecuta_la_instruccion_de_rechazo(): void
    {
        $this->micro->update(['estado' => 'inactivo']);

        $response = $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload());

        $response->assertSessionHasErrors('micro_id');
        $this->assertDatabaseCount('asignacion_turnos', 0);
    }

    public function test_cb_i04_store_conductor_inactivo_ejecuta_la_instruccion_de_rechazo(): void
    {
        $this->conductor->update(['estado' => 'inactivo']);

        $response = $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload());

        $response->assertSessionHasErrors('conductor_id');
        $this->assertDatabaseCount('asignacion_turnos', 0);
    }

    public function test_cb_i05_store_ruta_inactiva_ejecuta_la_instruccion_de_rechazo(): void
    {
        $this->ruta->update(['estado' => 'inactivo']);

        $response = $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload());

        $response->assertSessionHasErrors('ruta_id');
        $this->assertDatabaseCount('asignacion_turnos', 0);
    }

    public function test_cb_i06_store_conductor_duplicado_ejecuta_la_instruccion_de_rechazo(): void
    {
        $this->crearAsignacion(['micro_id' => $this->crearMicro()->id]);

        $response = $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload());

        $response->assertSessionHasErrors('conductor_id');
        $this->assertDatabaseCount('asignacion_turnos', 1);
    }

    public function test_cb_i07_store_micro_duplicado_ejecuta_la_instruccion_de_rechazo(): void
    {
        $this->crearAsignacion(['conductor_id' => $this->crearConductor()->id]);

        $response = $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload());

        $response->assertSessionHasErrors('micro_id');
        $this->assertDatabaseCount('asignacion_turnos', 1);
    }

    // ─────────────────────────────────────────────────────────────────
    // CAJA BLANCA · MÉTODO 2: COBERTURA DE DECISIONES (CB-D)
    // ─────────────────────────────────────────────────────────────────

    public function test_cb_d01_decision_turno_activo_rama_verdadera(): void
    {
        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('asignacion_turnos', 1);
    }

    public function test_cb_d01_decision_turno_activo_rama_falsa(): void
    {
        $this->turno->update(['estado' => 'inactivo']);

        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertSessionHasErrors('turno_id');

        $this->assertDatabaseCount('asignacion_turnos', 0);
    }

    public function test_cb_d02_decision_micro_existe_rama_falsa(): void
    {
        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload(['micro_id' => 999999]))
            ->assertSessionHasErrors('micro_id');

        $this->assertDatabaseCount('asignacion_turnos', 0);
    }

    public function test_cb_d03_decision_micro_activo_rama_falsa(): void
    {
        $this->micro->update(['estado' => 'inactivo']);

        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertSessionHasErrors('micro_id');

        $this->assertDatabaseCount('asignacion_turnos', 0);
    }

    public function test_cb_d04_decision_conductor_existe_rama_falsa(): void
    {
        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload(['conductor_id' => 999999]))
            ->assertSessionHasErrors('conductor_id');

        $this->assertDatabaseCount('asignacion_turnos', 0);
    }

    public function test_cb_d05_decision_conductor_activo_rama_falsa(): void
    {
        $this->conductor->update(['estado' => 'inactivo']);

        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertSessionHasErrors('conductor_id');

        $this->assertDatabaseCount('asignacion_turnos', 0);
    }

    public function test_cb_d06_decision_ruta_existe_rama_falsa(): void
    {
        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload(['ruta_id' => 999999]))
            ->assertSessionHasErrors('ruta_id');

        $this->assertDatabaseCount('asignacion_turnos', 0);
    }

    public function test_cb_d07_decision_ruta_activa_rama_falsa(): void
    {
        $this->ruta->update(['estado' => 'inactivo']);

        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertSessionHasErrors('ruta_id');

        $this->assertDatabaseCount('asignacion_turnos', 0);
    }

    public function test_cb_d08_decision_conductor_ocupado_rama_falsa(): void
    {
        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('asignacion_turnos', 1);
    }

    public function test_cb_d08_decision_conductor_ocupado_rama_verdadera(): void
    {
        $this->crearAsignacion(['micro_id' => $this->crearMicro()->id]);

        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertSessionHasErrors('conductor_id');

        $this->assertDatabaseCount('asignacion_turnos', 1);
    }

    public function test_cb_d09_decision_micro_ocupado_rama_verdadera(): void
    {
        $this->crearAsignacion(['conductor_id' => $this->crearConductor()->id]);

        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertSessionHasErrors('micro_id');

        $this->assertDatabaseCount('asignacion_turnos', 1);
    }

    // ─────────────────────────────────────────────────────────────────
    // CAJA BLANCA · MÉTODO 3: COBERTURA DE CONDICIONES (CB-C)
    // ─────────────────────────────────────────────────────────────────

    public function test_cb_c01_condiciones_a_y_b_verdaderas_micro_existente_y_activo(): void
    {
        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('asignacion_turnos', 1);
    }

    public function test_cb_c02_condicion_a_falsa_micro_inexistente(): void
    {
        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload(['micro_id' => 999999]))
            ->assertSessionHasErrors('micro_id');

        $this->assertDatabaseCount('asignacion_turnos', 0);
    }

    public function test_cb_c03_condicion_b_falsa_micro_inactivo(): void
    {
        $this->micro->update(['estado' => 'inactivo']);

        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertSessionHasErrors('micro_id');

        $this->assertDatabaseCount('asignacion_turnos', 0);
    }

    public function test_cb_c04_condiciones_a_y_b_verdaderas_conductor_existente_y_activo(): void
    {
        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('asignacion_turnos', 1);
    }

    public function test_cb_c05_condicion_a_falsa_conductor_inexistente(): void
    {
        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload(['conductor_id' => 999999]))
            ->assertSessionHasErrors('conductor_id');

        $this->assertDatabaseCount('asignacion_turnos', 0);
    }

    public function test_cb_c06_condicion_b_falsa_conductor_inactivo(): void
    {
        $this->conductor->update(['estado' => 'inactivo']);

        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertSessionHasErrors('conductor_id');

        $this->assertDatabaseCount('asignacion_turnos', 0);
    }

    // ─────────────────────────────────────────────────────────────────
    // CAJA BLANCA · MÉTODO 4: COBERTURA DE CAMINOS (CB-P)
    // ─────────────────────────────────────────────────────────────────

    public function test_cb_p01_camino_exitoso_sin_duplicidad(): void
    {
        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertRedirect(route('asignacion-turno.index'));

        $this->assertDatabaseCount('asignacion_turnos', 1);
        $this->assertDatabaseHas('asignacion_turnos', ['estado' => 'pendiente']);
    }

    public function test_cb_p02_camino_turno_inactivo(): void
    {
        $this->turno->update(['estado' => 'inactivo']);

        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertSessionHasErrors('turno_id');

        $this->assertDatabaseCount('asignacion_turnos', 0);
    }

    public function test_cb_p03_camino_micro_invalido(): void
    {
        $this->micro->update(['estado' => 'inactivo']);

        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertSessionHasErrors('micro_id');

        $this->assertDatabaseCount('asignacion_turnos', 0);
    }

    public function test_cb_p04_camino_conductor_invalido(): void
    {
        $this->conductor->update(['estado' => 'inactivo']);

        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertSessionHasErrors('conductor_id');

        $this->assertDatabaseCount('asignacion_turnos', 0);
    }

    public function test_cb_p05_camino_ruta_invalida(): void
    {
        $this->ruta->update(['estado' => 'inactivo']);

        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertSessionHasErrors('ruta_id');

        $this->assertDatabaseCount('asignacion_turnos', 0);
    }

    public function test_cb_p06_camino_conductor_duplicado(): void
    {
        $this->crearAsignacion(['micro_id' => $this->crearMicro()->id]);

        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertSessionHasErrors('conductor_id');

        $this->assertDatabaseCount('asignacion_turnos', 1);
    }

    public function test_cb_p07_camino_micro_duplicado(): void
    {
        $this->crearAsignacion(['conductor_id' => $this->crearConductor()->id]);

        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertSessionHasErrors('micro_id');

        $this->assertDatabaseCount('asignacion_turnos', 1);
    }

    public function test_cb_p08_camino_error_de_validacion_de_campos_obligatorios(): void
    {
        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), [])
            ->assertSessionHasErrors(['fecha', 'turno_id', 'ruta_id', 'micro_id', 'conductor_id']);

        $this->assertDatabaseCount('asignacion_turnos', 0);
    }

    // ─────────────────────────────────────────────────────────────────
    // CAJA NEGRA · MÉTODO 1: PARTICIÓN DE EQUIVALENCIA (CN-PE)
    // ─────────────────────────────────────────────────────────────────

    public function test_cn_pe01_clase_valida_micro_activo(): void
    {
        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('asignacion_turnos', ['micro_id' => $this->micro->id]);
    }

    public function test_cn_pe02_clase_invalida_micro_inactivo(): void
    {
        $this->micro->update(['estado' => 'inactivo']);

        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertSessionHasErrors('micro_id');

        $this->assertDatabaseCount('asignacion_turnos', 0);
    }

    public function test_cn_pe03_clase_invalida_micro_inexistente(): void
    {
        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload(['micro_id' => 999999]))
            ->assertSessionHasErrors('micro_id');

        $this->assertDatabaseCount('asignacion_turnos', 0);
    }

    // ─────────────────────────────────────────────────────────────────
    // CAJA NEGRA · MÉTODO 2: ANÁLISIS DE VALORES LÍMITE (CN-VL)
    // Campo: observaciones (nullable, máximo 1000 caracteres)
    // ─────────────────────────────────────────────────────────────────

    public function test_cn_vl01_observaciones_ausente_limite_inferior_cero(): void
    {
        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('asignacion_turnos', ['observaciones' => null]);
    }

    public function test_cn_vl02_observaciones_vacia_limite_inferior_cero(): void
    {
        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload(['observaciones' => '']))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('asignacion_turnos', 1);
    }

    public function test_cn_vl03_observaciones_un_caracter_dentro_del_rango(): void
    {
        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload(['observaciones' => 'A']))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('asignacion_turnos', ['observaciones' => 'A']);
    }

    public function test_cn_vl04_observaciones_999_caracteres_cerca_del_limite_superior(): void
    {
        $texto = str_repeat('A', 999);

        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload(['observaciones' => $texto]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('asignacion_turnos', ['observaciones' => $texto]);
    }

    public function test_cn_vl05_observaciones_1000_caracteres_limite_superior(): void
    {
        $texto = str_repeat('B', 1000);

        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload(['observaciones' => $texto]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('asignacion_turnos', ['observaciones' => $texto]);
    }

    public function test_cn_vl06_observaciones_1001_caracteres_encima_del_limite_superior(): void
    {
        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload(['observaciones' => str_repeat('C', 1001)]))
            ->assertSessionHasErrors('observaciones');

        $this->assertDatabaseCount('asignacion_turnos', 0);
    }

    // ─────────────────────────────────────────────────────────────────
    // CAJA NEGRA · MÉTODO 3: TABLA DE DECISIÓN (CN-TD)
    // Condiciones: recursos activos / sin duplicidad en fecha y turno
    // ─────────────────────────────────────────────────────────────────

    public function test_cn_td01_recursos_activos_y_sin_duplicidad_crea_asignacion(): void
    {
        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('asignacion_turnos', 1);
        $this->assertDatabaseHas('asignacion_turnos', ['estado' => 'pendiente']);
    }

    public function test_cn_td02_recursos_activos_con_duplicidad_rechaza_por_conductor(): void
    {
        $this->crearAsignacion(['micro_id' => $this->crearMicro()->id]);

        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertSessionHasErrors('conductor_id');

        $this->assertDatabaseCount('asignacion_turnos', 1);
    }

    public function test_cn_td03_recurso_inactivo_sin_duplicidad_rechaza_por_micro(): void
    {
        $this->micro->update(['estado' => 'inactivo']);

        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertSessionHasErrors('micro_id');

        $this->assertDatabaseCount('asignacion_turnos', 0);
    }

    public function test_cn_td04_recurso_inactivo_con_duplicidad_prevalece_error_de_recurso(): void
    {
        $this->crearAsignacion(['micro_id' => $this->crearMicro()->id]);
        $this->micro->update(['estado' => 'inactivo']);

        $response = $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload());

        $response->assertSessionHasErrors('micro_id');
        $response->assertSessionDoesntHaveErrors('conductor_id');
        $this->assertDatabaseCount('asignacion_turnos', 1);
    }

    // ─────────────────────────────────────────────────────────────────
    // CAJA NEGRA · MÉTODO 4: TRANSICIÓN DE ESTADOS (CN-TE)
    // ─────────────────────────────────────────────────────────────────

    public function test_cn_te01_sin_asignacion_mas_store_valido_pasa_a_pendiente(): void
    {
        $this->assertDatabaseCount('asignacion_turnos', 0);

        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertRedirect(route('asignacion-turno.index'));

        $this->assertDatabaseCount('asignacion_turnos', 1);
        $this->assertDatabaseHas('asignacion_turnos', ['estado' => 'pendiente']);
    }

    public function test_cn_te02_sin_asignacion_mas_store_invalido_permanece_sin_asignacion(): void
    {
        $this->turno->update(['estado' => 'inactivo']);

        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertSessionHasErrors('turno_id');

        $this->assertDatabaseCount('asignacion_turnos', 0);
    }

    public function test_cn_te03_asignacion_pendiente_mas_store_duplicado_permanece_pendiente(): void
    {
        $existente = $this->crearAsignacion(['estado' => 'pendiente', 'micro_id' => $this->crearMicro()->id]);

        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertSessionHasErrors('conductor_id');

        $this->assertDatabaseCount('asignacion_turnos', 1);
        $this->assertSame('pendiente', $existente->fresh()->estado);
    }

    public function test_cn_te04_asignacion_cancelada_mas_store_valido_crea_nueva_pendiente(): void
    {
        $cancelada = $this->crearAsignacion(['estado' => 'cancelado', 'micro_id' => $this->crearMicro()->id]);

        $this->actingAs($this->admin)
            ->post(route('asignacion-turno.store'), $this->payload())
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('asignacion_turnos', 2);
        $this->assertSame('cancelado', $cancelada->fresh()->estado);
        $this->assertDatabaseHas('asignacion_turnos', [
            'conductor_id' => $this->conductor->id,
            'fecha' => now()->toDateString(),
            'turno_id' => $this->turno->id,
            'estado' => 'pendiente',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // Utilidades
    // ─────────────────────────────────────────────────────────────────

    /**
     * @param  array<string, mixed>  $sobrescribir
     * @return array<string, mixed>
     */
    private function payload(array $sobrescribir = []): array
    {
        return array_merge([
            'fecha' => now()->toDateString(),
            'turno_id' => $this->turno->id,
            'ruta_id' => $this->ruta->id,
            'micro_id' => $this->micro->id,
            'conductor_id' => $this->conductor->id,
        ], $sobrescribir);
    }

    /**
     * @param  array<string, mixed>  $sobrescribir
     */
    private function crearAsignacion(array $sobrescribir = []): AsignacionTurno
    {
        return AsignacionTurno::create(array_merge($this->payload(), [
            'estado' => 'pendiente',
        ], $sobrescribir));
    }

    private function crearRuta(): Ruta
    {
        $numero = ++$this->secuencia;

        return Ruta::create([
            'nombre' => "Ruta Prueba {$numero}",
            'descripcion' => 'Ruta creada para las pruebas',
            'estado' => 'activo',
        ]);
    }

    private function crearMicro(string $estado = 'activo'): Micro
    {
        $numero = ++$this->secuencia;

        return Micro::create([
            'propietario_id' => $this->propietario->id,
            'placa' => "TEST-{$numero}",
            'chasis' => "CHASIS-{$numero}",
            'anio_fabricacion' => 2020,
            'modelo' => 'Coaster',
            'marca' => 'Toyota',
            'capacidad_pasajeros' => 30,
            'estado' => $estado,
        ]);
    }

    private function crearConductor(string $estado = 'activo'): Conductor
    {
        $numero = ++$this->secuencia;

        return Conductor::create([
            'nombre' => 'Conductor',
            'apellido' => "Prueba {$numero}",
            'telefono' => '70000000',
            'correo' => "conductor{$numero}@linea61.test",
            'ci' => "800000{$numero}",
            'licencia' => "LIC-{$numero}",
            'categoria_licencia' => 'C',
            'estado' => $estado,
        ]);
    }
}
