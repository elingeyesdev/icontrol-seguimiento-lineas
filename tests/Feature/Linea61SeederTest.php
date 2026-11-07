<?php

namespace Tests\Feature;

use App\Models\Parada;
use App\Models\Ruta;
use App\Models\RutaParada;
use Database\Seeders\Linea61Seeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Linea61SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_linea_61_seeder_creates_stops_with_coordinates(): void
    {
        $this->seed(Linea61Seeder::class);

        $ruta = Ruta::where('nombre', '61 (Azul)')->firstOrFail();

        $paradas = Parada::where('estado', 'activo')->get();

        $this->assertCount(16, $paradas);
        $this->assertTrue(
            $paradas->every(fn (Parada $parada): bool => $parada->latitud !== null && $parada->longitud !== null),
            'Todas las paradas sembradas deben tener latitud y longitud.',
        );

        $this->assertSame(13, RutaParada::where('ruta_id', $ruta->id)->where('sentido', 'Ida')->count());
        $this->assertSame(16, RutaParada::where('ruta_id', $ruta->id)->where('sentido', 'Vuelta')->count());
    }

    public function test_linea_61_seeder_respects_the_real_route_direction(): void
    {
        $this->seed(Linea61Seeder::class);

        $ruta = Ruta::where('nombre', '61 (Azul)')->firstOrFail();

        $ida = RutaParada::with('parada')
            ->where('ruta_id', $ruta->id)
            ->where('sentido', 'Ida')
            ->orderBy('orden')
            ->get()
            ->pluck('parada.nombre');

        $vuelta = RutaParada::with('parada')
            ->where('ruta_id', $ruta->id)
            ->where('sentido', 'Vuelta')
            ->orderBy('orden')
            ->get()
            ->pluck('parada.nombre');

        $this->assertSame('Urbanización El Retoño', $ida->first());
        $this->assertSame('Ex-Terminal (La Ramada)', $ida->last());

        $this->assertSame('Ex-Terminal (La Ramada)', $vuelta->first());
        $this->assertSame('Urbanización El Retoño', $vuelta->last());
    }

    public function test_linea_61_seeder_is_idempotent_and_updates_existing_stop_coordinates(): void
    {
        $parada = Parada::create([
            'nombre' => 'Terminal Bimodal',
            'referencia' => 'Anterior',
            'latitud' => null,
            'longitud' => null,
            'estado' => 'activo',
        ]);

        $this->seed(Linea61Seeder::class);
        $this->seed(Linea61Seeder::class);

        $parada->refresh();

        $this->assertNotNull($parada->latitud);
        $this->assertNotNull($parada->longitud);
        $this->assertSame(16, Parada::where('estado', 'activo')->count());
    }
}
