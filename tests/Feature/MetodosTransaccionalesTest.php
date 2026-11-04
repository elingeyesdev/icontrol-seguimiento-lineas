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
use App\Services\AsignacionTurnoService;
use App\Services\ControlRecorridoService;
use App\Services\SeguimientoGpsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Pruebas focalizadas en un método principal por cada transacción:
 * Asignación de Turno, Seguimiento GPS y Control de Recorrido.
 */
class MetodosTransaccionalesTest extends TestCase
{
    use RefreshDatabase;

    private Turno $turnoActivo;

    private Turno $turnoInactivo;

    private Ruta $ruta;

    private Conductor $conductor;

    private Conductor $otroConductor;

    private Micro $micro;

    private Micro $otroMicro;

    protected function setUp(): void
    {
        parent::setUp();

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
            'nombre' => 'Ruta Métodos 61',
            'estado' => 'activo',
        ]);

        $this->conductor = Conductor::create([
            'nombre' => 'Carlos',
            'apellido' => 'Gómez',
            'licencia' => 'LIC-MT-01',
            'estado' => 'activo',
        ]);

        $this->otroConductor = Conductor::create([
            'nombre' => 'Ana',
            'apellido' => 'Rojas',
            'licencia' => 'LIC-MT-02',
            'estado' => 'activo',
        ]);

        $propietario = Propietario::create([
            'nombre' => 'Carlos',
            'apellido' => 'Suárez',
            'ci' => '9000001',
            'estado' => 'activo',
        ]);

        $this->micro = Micro::create([
            'propietario_id' => $propietario->id,
            'placa' => 'MT01-TEST',
            'marca' => 'Toyota',
            'modelo' => 'Coaster',
            'capacidad_pasajeros' => 30,
            'estado' => 'activo',
        ]);

        $this->otroMicro = Micro::create([
            'propietario_id' => $propietario->id,
            'placa' => 'MT02-TEST',
            'marca' => 'Toyota',
            'modelo' => 'Coaster',
            'capacidad_pasajeros' => 30,
            'estado' => 'activo',
        ]);
    }

    // -----------------------------------------------------------------
    // Transacción 1: Asignación de Turno
    // Método analizado: AsignacionTurnoService::crear()
    // -----------------------------------------------------------------

    public function test_at01_alta_valida_crea_asignacion_pendiente_e_ignora_estado_y_horas(): void
    {
        $asignacion = $this->asignacionService()->crear($this->payloadAlta([
            'estado' => 'en_curso',
            'hora_salida' => '05:00:00',
            'hora_llegada' => '13:00:00',
        ]));

        $this->assertSame('pendiente', $asignacion->estado);
        $this->assertNull($asignacion->hora_salida);
        $this->assertNull($asignacion->hora_llegada);
        $this->assertDatabaseHas('asignacion_turnos', [
            'id' => $asignacion->id,
            'estado' => 'pendiente',
            'hora_salida' => null,
            'hora_llegada' => null,
        ]);
    }

    public function test_at02_alta_rechaza_turno_inactivo(): void
    {
        $this->expectException(ValidationException::class);

        try {
            $this->asignacionService()->crear($this->payloadAlta([
                'turno_id' => $this->turnoInactivo->id,
            ]));
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('turno_id', $e->errors());

            throw $e;
        }
    }

    public function test_at03_alta_rechaza_conductor_duplicado_en_fecha_y_turno(): void
    {
        $this->crearAsignacion([
            'conductor_id' => $this->conductor->id,
            'micro_id' => $this->micro->id,
        ]);

        $this->expectException(ValidationException::class);

        try {
            $this->asignacionService()->crear($this->payloadAlta([
                'conductor_id' => $this->conductor->id,
                'micro_id' => $this->otroMicro->id,
            ]));
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('conductor_id', $e->errors());

            throw $e;
        }
    }

    public function test_at04_alta_rechaza_micro_duplicado_en_fecha_y_turno(): void
    {
        $this->crearAsignacion([
            'conductor_id' => $this->otroConductor->id,
            'micro_id' => $this->micro->id,
        ]);

        $this->expectException(ValidationException::class);

        try {
            $this->asignacionService()->crear($this->payloadAlta([
                'conductor_id' => $this->conductor->id,
                'micro_id' => $this->micro->id,
            ]));
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('micro_id', $e->errors());

            throw $e;
        }
    }

    public function test_at05_alta_rechaza_micro_inactivo(): void
    {
        $this->micro->update(['estado' => 'inactivo']);

        $this->expectException(ValidationException::class);

        try {
            $this->asignacionService()->crear($this->payloadAlta());
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('micro_id', $e->errors());

            throw $e;
        }
    }

    // -----------------------------------------------------------------
    // Transacción 2: Seguimiento GPS
    // Método analizado: SeguimientoGpsService::registrarPunto()
    // -----------------------------------------------------------------

    public function test_sg01_registra_punto_en_geocerca_y_marca_la_parada_cumplida(): void
    {
        [$asignacion, $parada1] = $this->crearRecorridoConDosParadas();

        $resultado = $this->gpsService()->registrarPunto($asignacion, [
            'fecha_hora_gps' => now()->subMinute()->toDateTimeString(),
            'latitud' => (float) $parada1->latitud,
            'longitud' => (float) $parada1->longitud,
            'velocidad' => 12.0,
        ]);

        $control = $resultado['control_recorrido'];

        $this->assertSame('cumplido', $control->estado);
        $this->assertNotNull($control->ruta_parada_id);
        $this->assertLessThanOrEqual(ControlRecorridoService::RADIO_TOLERANCIA_METROS, $control->distancia_metros);
        $this->assertSame('en_curso', $asignacion->fresh()->estado);
        $this->assertDatabaseCount('seguimiento_gps', 1);
    }

    public function test_sg02_registra_punto_fuera_de_ruta_en_el_control_general(): void
    {
        [$asignacion] = $this->crearRecorridoConDosParadas();

        $resultado = $this->gpsService()->registrarPunto($asignacion, [
            'fecha_hora_gps' => now()->subMinute()->toDateTimeString(),
            'latitud' => -17.76165300,
            'longitud' => -63.16200000,
            'velocidad' => 25.0,
        ]);

        $control = $resultado['control_recorrido'];

        $this->assertNull($control->ruta_parada_id);
        $this->assertSame('Seguimiento en ruta', $control->observacion);
        $this->assertSame('en_curso', $control->estado);
        $this->assertDatabaseHas('seguimiento_gps', [
            'control_recorrido_id' => $control->id,
        ]);
    }

    public function test_sg03_punto_repetido_no_genera_un_segundo_registro(): void
    {
        [$asignacion] = $this->crearRecorridoConDosParadas();

        $punto = [
            'fecha_hora_gps' => now()->subMinute()->toDateTimeString(),
            'latitud' => -17.76165300,
            'longitud' => -63.16200000,
            'velocidad' => 25.0,
        ];

        $this->gpsService()->registrarPunto($asignacion, $punto);
        $this->gpsService()->registrarPunto($asignacion, $punto);

        $this->assertDatabaseCount('seguimiento_gps', 1);
    }

    public function test_sg04_rechaza_punto_cuando_la_asignacion_esta_completada(): void
    {
        $asignacion = $this->crearAsignacion(['estado' => 'completado']);

        $this->expectException(ValidationException::class);

        try {
            $this->gpsService()->registrarPunto($asignacion, [
                'fecha_hora_gps' => now()->subMinute()->toDateTimeString(),
                'latitud' => -17.78300000,
                'longitud' => -63.18200000,
                'velocidad' => 10.0,
            ]);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('asignacion_turno_id', $e->errors());

            throw $e;
        }
    }

    public function test_sg05_rechaza_fecha_hora_gps_mayor_al_margen_de_dos_minutos(): void
    {
        [$asignacion] = $this->crearRecorridoConDosParadas();

        $this->expectException(ValidationException::class);

        try {
            $this->gpsService()->registrarPunto($asignacion, [
                'fecha_hora_gps' => now()->addMinutes(3)->toDateTimeString(),
                'latitud' => -17.78300000,
                'longitud' => -63.18200000,
                'velocidad' => 10.0,
            ]);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('fecha_hora_gps', $e->errors());

            throw $e;
        }
    }

    // -----------------------------------------------------------------
    // Transacción 3: Control de Recorrido
    // Método analizado: ControlRecorridoService::evaluarPunto()
    // -----------------------------------------------------------------

    public function test_cr01_punto_dentro_de_la_geocerca_cumple_la_parada(): void
    {
        [$asignacion, $parada1] = $this->crearRecorridoConDosParadas();

        $control = $this->controlService()->evaluarPunto(
            $asignacion,
            $this->gpsEn($parada1, now()->subMinute())
        );

        $this->assertNotNull($control);
        $this->assertSame('cumplido', $control->estado);
        $this->assertSame($parada1->id, $control->rutaParada->parada_id);
        $this->assertLessThanOrEqual(ControlRecorridoService::RADIO_TOLERANCIA_METROS, $control->distancia_metros);
        $this->assertSame('en_curso', $asignacion->fresh()->estado);
    }

    public function test_cr02_punto_fuera_de_la_geocerca_no_cumple_ninguna_parada(): void
    {
        [$asignacion] = $this->crearRecorridoConDosParadas();

        $this->controlService()->planificar($asignacion);

        $control = $this->controlService()->evaluarPunto(
            $asignacion,
            new SeguimientoGps([
                'fecha_hora_gps' => now()->subMinute()->toDateTimeString(),
                'latitud' => -17.76165300,
                'longitud' => -63.16200000,
                'velocidad' => 25.0,
            ])
        );

        $this->assertNull($control);
        $this->assertSame(0, ControlRecorrido::where('asignacion_turno_id', $asignacion->id)->where('estado', 'cumplido')->count());
    }

    public function test_cr03_parada_posterior_marca_las_intermedias_como_omitidas(): void
    {
        $ruta = Ruta::create(['nombre' => 'Ruta Omisión 61', 'estado' => 'activo']);

        $paradas = collect([
            ['nombre' => 'O1', 'latitud' => -17.78300000, 'longitud' => -63.18200000, 'orden' => 1],
            ['nombre' => 'O2', 'latitud' => -17.78400000, 'longitud' => -63.18300000, 'orden' => 2],
            ['nombre' => 'O3', 'latitud' => -17.78500000, 'longitud' => -63.18400000, 'orden' => 3],
        ])->map(function (array $datos) use ($ruta) {
            $parada = Parada::create($datos + ['estado' => 'activo']);

            RutaParada::create([
                'ruta_id' => $ruta->id,
                'parada_id' => $parada->id,
                'orden' => $datos['orden'],
                'sentido' => 'Ida',
                'estado' => 'activo',
            ]);

            return $parada;
        });

        $asignacion = $this->crearAsignacion([
            'estado' => 'en_curso',
            'ruta_id' => $ruta->id,
        ]);

        $this->controlService()->planificar($asignacion);

        $control = $this->controlService()->evaluarPunto(
            $asignacion,
            $this->gpsEn($paradas[2], now()->subMinute())
        );

        $this->assertNotNull($control);
        $this->assertSame('cumplido', $control->estado);

        $omitidas = ControlRecorrido::where('asignacion_turno_id', $asignacion->id)
            ->where('estado', 'omitido')
            ->count();

        $this->assertSame(2, $omitidas);
    }

    public function test_cr04_ultima_parada_cierra_automaticamente_la_asignacion(): void
    {
        [$asignacion, , $parada2] = $this->crearRecorridoConDosParadas();

        $this->controlService()->planificar($asignacion);

        $this->controlService()->evaluarPunto(
            $asignacion,
            $this->gpsEn($parada2, now()->subMinute())
        );

        $asignacion->refresh();

        $this->assertSame('completado', $asignacion->estado);
        $this->assertNotNull($asignacion->hora_llegada);
    }

    public function test_cr05_al_terminar_ida_la_secuencia_continua_en_vuelta(): void
    {
        $ruta = Ruta::create(['nombre' => 'Ruta Circuito Métodos', 'estado' => 'activo']);

        $ida = $this->crearParadaDeRuta($ruta, 'I1', 'Ida', 1, -17.78300000, -63.18200000);
        $vuelta = $this->crearParadaDeRuta($ruta, 'V1', 'Vuelta', 1, -17.78400000, -63.18300000);

        $asignacion = $this->crearAsignacion([
            'estado' => 'en_curso',
            'ruta_id' => $ruta->id,
        ]);

        $this->controlService()->planificar($asignacion);

        $this->controlService()->evaluarPunto($asignacion, $this->gpsEn($ida, now()->subMinutes(2)));
        $this->assertSame('en_curso', $asignacion->fresh()->estado);

        $control = $this->controlService()->evaluarPunto($asignacion, $this->gpsEn($vuelta, now()->subMinute()));

        $this->assertNotNull($control);
        $this->assertSame('cumplido', $control->estado);
        $this->assertStringContainsString('[Vuelta]', $control->observacion);
        $this->assertSame('completado', $asignacion->fresh()->estado);
    }

    // -----------------------------------------------------------------
    // Utilidades
    // -----------------------------------------------------------------

    private function asignacionService(): AsignacionTurnoService
    {
        return app(AsignacionTurnoService::class);
    }

    private function gpsService(): SeguimientoGpsService
    {
        return app(SeguimientoGpsService::class);
    }

    private function controlService(): ControlRecorridoService
    {
        return app(ControlRecorridoService::class);
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private function payloadAlta(array $extra = []): array
    {
        return array_merge([
            'fecha' => now()->toDateString(),
            'turno_id' => $this->turnoActivo->id,
            'ruta_id' => $this->ruta->id,
            'micro_id' => $this->micro->id,
            'conductor_id' => $this->conductor->id,
        ], $extra);
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private function crearAsignacion(array $extra = []): AsignacionTurno
    {
        return AsignacionTurno::create(array_merge([
            'fecha' => now()->toDateString(),
            'turno_id' => $this->turnoActivo->id,
            'ruta_id' => $this->ruta->id,
            'micro_id' => $this->micro->id,
            'conductor_id' => $this->conductor->id,
            'estado' => 'pendiente',
        ], $extra));
    }

    /**
     * Crea una ruta con dos paradas de Ida y una asignación en curso.
     *
     * @return array{0: AsignacionTurno, 1: Parada, 2: Parada}
     */
    private function crearRecorridoConDosParadas(): array
    {
        $ruta = Ruta::create(['nombre' => 'Ruta Geocerca 61', 'estado' => 'activo']);

        $parada1 = $this->crearParadaDeRuta($ruta, 'G1', 'Ida', 1, -17.78300000, -63.18200000);
        $parada2 = $this->crearParadaDeRuta($ruta, 'G2', 'Ida', 2, -17.79000000, -63.19000000);

        $asignacion = $this->crearAsignacion([
            'estado' => 'en_curso',
            'ruta_id' => $ruta->id,
        ]);

        return [$asignacion, $parada1, $parada2];
    }

    private function crearParadaDeRuta(
        Ruta $ruta,
        string $nombre,
        string $sentido,
        int $orden,
        float $latitud,
        float $longitud
    ): Parada {
        $parada = Parada::create([
            'nombre' => $nombre,
            'latitud' => $latitud,
            'longitud' => $longitud,
            'estado' => 'activo',
        ]);

        RutaParada::create([
            'ruta_id' => $ruta->id,
            'parada_id' => $parada->id,
            'orden' => $orden,
            'sentido' => $sentido,
            'estado' => 'activo',
        ]);

        return $parada;
    }

    private function gpsEn(Parada $parada, \DateTimeInterface $fechaHora): SeguimientoGps
    {
        return new SeguimientoGps([
            'fecha_hora_gps' => $fechaHora->format('Y-m-d H:i:s'),
            'latitud' => (float) $parada->latitud,
            'longitud' => (float) $parada->longitud,
            'velocidad' => 10.0,
        ]);
    }
}
