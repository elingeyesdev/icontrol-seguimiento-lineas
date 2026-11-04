<?php

namespace Tests\Feature;

use App\Models\Parada;
use App\Models\Ruta;
use App\Services\RutaParametrizacionService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ParametrizacionRutaTransaccionTest extends TestCase
{
    use RefreshDatabase;

    private Ruta $ruta;

    private Parada $parada1;

    private Parada $parada2;

    private Parada $parada3;

    private Parada $parada4;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ruta = Ruta::create([
            'nombre' => 'Ruta Parametrización Test',
            'estado' => 'activo',
        ]);

        $this->parada1 = $this->crearParada('Parada 1');
        $this->parada2 = $this->crearParada('Parada 2');
        $this->parada3 = $this->crearParada('Parada 3');
        $this->parada4 = $this->crearParada('Parada 4');
    }

    public function test_rp01_guarda_ida_y_vuelta_en_orden(): void
    {
        $this->service()->guardarParametrizacion(
            $this->ruta,
            [$this->parada2->id, $this->parada1->id],
            [$this->parada3->id, $this->parada4->id]
        );

        $this->assertSame(
            [[$this->parada2->id, 1], [$this->parada1->id, 2]],
            $this->paradasDe('Ida')
        );

        $this->assertSame(
            [[$this->parada3->id, 1], [$this->parada4->id, 2]],
            $this->paradasDe('Vuelta')
        );
    }

    public function test_rp02_reemplaza_solo_el_sentido_enviado(): void
    {
        $this->service()->guardarParametrizacion(
            $this->ruta,
            [$this->parada1->id, $this->parada2->id],
            [$this->parada3->id]
        );

        $this->service()->guardarParametrizacion(
            $this->ruta,
            [$this->parada4->id],
            null
        );

        $this->assertSame([[$this->parada4->id, 1]], $this->paradasDe('Ida'));
        $this->assertSame([[$this->parada3->id, 1]], $this->paradasDe('Vuelta'));
    }

    public function test_rp03_array_vacio_elimina_el_sentido(): void
    {
        $this->service()->guardarParametrizacion(
            $this->ruta,
            [$this->parada1->id, $this->parada2->id],
            [$this->parada3->id]
        );

        $this->service()->guardarParametrizacion($this->ruta, [], null);

        $this->assertSame([], $this->paradasDe('Ida'));
        $this->assertSame([[$this->parada3->id, 1]], $this->paradasDe('Vuelta'));
    }

    public function test_rp04_deduplica_y_descarta_valores_vacios(): void
    {
        $this->service()->guardarParametrizacion(
            $this->ruta,
            [$this->parada1->id, $this->parada1->id, $this->parada2->id, 0, null],
            [$this->parada3->id, $this->parada3->id]
        );

        $this->assertSame(
            [[$this->parada1->id, 1], [$this->parada2->id, 2]],
            $this->paradasDe('Ida')
        );

        $this->assertSame([[$this->parada3->id, 1]], $this->paradasDe('Vuelta'));
        $this->assertSame(3, DB::table('parada_ruta')->where('ruta_id', $this->ruta->id)->count());
    }

    public function test_rp05_ambos_null_no_modifica_la_parametrizacion(): void
    {
        $this->service()->guardarParametrizacion(
            $this->ruta,
            [$this->parada1->id],
            [$this->parada2->id]
        );

        $antesIda = $this->paradasDe('Ida');
        $antesVuelta = $this->paradasDe('Vuelta');

        $this->service()->guardarParametrizacion($this->ruta, null, null);

        $this->assertSame($antesIda, $this->paradasDe('Ida'));
        $this->assertSame($antesVuelta, $this->paradasDe('Vuelta'));
    }

    public function test_rp07_reemplaza_solo_vuelta(): void
    {
        $this->service()->guardarParametrizacion(
            $this->ruta,
            [$this->parada1->id],
            [$this->parada2->id]
        );

        $this->service()->guardarParametrizacion(
            $this->ruta,
            null,
            [$this->parada3->id, $this->parada4->id]
        );

        $this->assertSame([[$this->parada1->id, 1]], $this->paradasDe('Ida'));
        $this->assertSame(
            [[$this->parada3->id, 1], [$this->parada4->id, 2]],
            $this->paradasDe('Vuelta')
        );
    }

    public function test_rp06_falla_de_insercion_revierte_el_borrado_previo(): void
    {
        $this->service()->guardarParametrizacion($this->ruta, [$this->parada1->id], null);

        try {
            $this->service()->guardarParametrizacion($this->ruta, [999999], null);
            $this->fail('Se esperaba una excepción por la parada inexistente.');
        } catch (QueryException) {
            // La clave foránea impide insertar una parada inexistente.
        }

        $this->assertSame([[$this->parada1->id, 1]], $this->paradasDe('Ida'));
    }

    private function service(): RutaParametrizacionService
    {
        return app(RutaParametrizacionService::class);
    }

    private function crearParada(string $nombre): Parada
    {
        return Parada::create([
            'nombre' => $nombre,
            'latitud' => -17.78300000,
            'longitud' => -63.18200000,
            'estado' => 'activo',
        ]);
    }

    /**
     * @return array<int, array{0: int, 1: int}>
     */
    private function paradasDe(string $sentido): array
    {
        return DB::table('parada_ruta')
            ->where('ruta_id', $this->ruta->id)
            ->where('sentido', $sentido)
            ->orderBy('orden')
            ->get(['parada_id', 'orden'])
            ->map(fn ($fila) => [(int) $fila->parada_id, (int) $fila->orden])
            ->all();
    }
}
