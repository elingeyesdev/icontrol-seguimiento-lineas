<?php

namespace Database\Seeders;

use App\Models\Parada;
use App\Models\Ruta;
use App\Models\RutaParada;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Linea61Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Recorrido real de la línea 61 (Azul), operada por Microcruz / Mi Micro
     * Santa Cruz. La "ida" va de Urb. El Retoño a la Ex-Terminal (La Ramada)
     * y la "vuelta" hace el recorrido inverso por Av. Brasil y los anillos.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $ruta = Ruta::query()->updateOrCreate(
                ['nombre' => '61 (Azul)'],
                Ruta::factory()->linea61Azul()->make()->getAttributes(),
            );

            $paradas = [
                'el-retono' => [
                    'nombre' => 'Urbanización El Retoño',
                    'referencia' => 'Parada final, ingreso a la urbanización',
                    'latitud' => -17.73941120,
                    'longitud' => -63.07288210,
                ],
                'virgen-de-lujan' => [
                    'nombre' => 'Av. Virgen de Luján',
                    'referencia' => 'Trazado entre El Retoño y Pampa de la Isla',
                    'latitud' => -17.74500000,
                    'longitud' => -63.09500000,
                ],
                'pampa-de-la-isla' => [
                    'nombre' => 'Pampa de la Isla',
                    'referencia' => 'Carretera a Cotoca',
                    'latitud' => -17.75953540,
                    'longitud' => -63.11580100,
                ],
                'cruce-del-trillo' => [
                    'nombre' => 'Cruce del Trillo',
                    'referencia' => 'Cruce de la vía férrea, retorno',
                    'latitud' => -17.77600000,
                    'longitud' => -63.13500000,
                ],
                'av-genecheru' => [
                    'nombre' => 'Av. Genecherú',
                    'referencia' => 'Urbanización La Cruz',
                    'latitud' => -17.78200000,
                    'longitud' => -63.13100000,
                ],
                'barrio-los-andes' => [
                    'nombre' => 'Barrio Los Andes',
                    'referencia' => 'Av. 16 de Julio',
                    'latitud' => -17.78840000,
                    'longitud' => -63.12730000,
                ],
                'villa-primero-de-mayo' => [
                    'nombre' => 'Villa Primero de Mayo',
                    'referencia' => 'Av. Cumavi',
                    'latitud' => -17.79250000,
                    'longitud' => -63.12630000,
                ],
                'pampa-de-la-cruz' => [
                    'nombre' => 'Barrio Pampa de la Cruz',
                    'referencia' => 'Av. Cumavi',
                    'latitud' => -17.79300000,
                    'longitud' => -63.12700000,
                ],
                'feria-cumavi' => [
                    'nombre' => 'Feria Cumavi',
                    'referencia' => 'Feria de la Villa Primero de Mayo',
                    'latitud' => -17.79250000,
                    'longitud' => -63.12850000,
                ],
                'feria-barrio-lindo' => [
                    'nombre' => 'Feria Barrio Lindo',
                    'referencia' => 'Av. Tres Pasos al Frente',
                    'latitud' => -17.78994240,
                    'longitud' => -63.15268520,
                ],
                'terminal-bimodal' => [
                    'nombre' => 'Terminal Bimodal',
                    'referencia' => 'Av. Intermodal, trenes y buses',
                    'latitud' => -17.78992760,
                    'longitud' => -63.16126380,
                ],
                'av-brasil' => [
                    'nombre' => 'Av. Brasil',
                    'referencia' => 'Trazado hacia el Primer Anillo',
                    'latitud' => -17.78570000,
                    'longitud' => -63.16410000,
                ],
                'segundo-anillo' => [
                    'nombre' => 'Segundo Anillo (Av. Brasil)',
                    'referencia' => 'Intersección Av. Brasil y Segundo Anillo',
                    'latitud' => -17.78570000,
                    'longitud' => -63.16600000,
                ],
                'tercer-anillo' => [
                    'nombre' => 'Tercer Anillo',
                    'referencia' => 'Intersección con Av. Brasil, zona Feria Barrio Lindo',
                    'latitud' => -17.78570000,
                    'longitud' => -63.15490000,
                ],
                'primer-anillo' => [
                    'nombre' => 'Primer Anillo',
                    'referencia' => 'Intersección con Av. Brasil, giro al sur',
                    'latitud' => -17.78540000,
                    'longitud' => -63.17150000,
                ],
                'ex-terminal' => [
                    'nombre' => 'Ex-Terminal (La Ramada)',
                    'referencia' => 'Av. Grigotá',
                    'latitud' => -17.79330140,
                    'longitud' => -63.18699280,
                ],
            ];

            $paradaIds = [];

            foreach ($paradas as $clave => $datos) {
                $paradaIds[$clave] = Parada::query()->updateOrCreate(
                    ['nombre' => $datos['nombre']],
                    [...$datos, 'estado' => 'activo'],
                )->id;
            }

            // Ida: Urb. El Retoño -> Ex-Terminal (La Ramada).
            $sentidos = [
                'Ida' => [
                    'el-retono',
                    'virgen-de-lujan',
                    'pampa-de-la-isla',
                    'cruce-del-trillo',
                    'av-genecheru',
                    'barrio-los-andes',
                    'villa-primero-de-mayo',
                    'feria-cumavi',
                    'feria-barrio-lindo',
                    'terminal-bimodal',
                    'av-brasil',
                    'primer-anillo',
                    'ex-terminal',
                ],
                // Vuelta: Ex-Terminal -> Urb. El Retoño, vía Av. Brasil y los anillos.
                'Vuelta' => [
                    'ex-terminal',
                    'primer-anillo',
                    'av-brasil',
                    'segundo-anillo',
                    'tercer-anillo',
                    'feria-barrio-lindo',
                    'terminal-bimodal',
                    'villa-primero-de-mayo',
                    'feria-cumavi',
                    'pampa-de-la-cruz',
                    'barrio-los-andes',
                    'av-genecheru',
                    'cruce-del-trillo',
                    'pampa-de-la-isla',
                    'virgen-de-lujan',
                    'el-retono',
                ],
            ];

            RutaParada::query()->where('ruta_id', $ruta->id)->delete();

            foreach ($sentidos as $sentido => $clavesParada) {
                foreach ($clavesParada as $orden => $claveParada) {
                    RutaParada::query()->create([
                        'ruta_id' => $ruta->id,
                        'parada_id' => $paradaIds[$claveParada],
                        'orden' => $orden + 1,
                        'sentido' => $sentido,
                        'estado' => 'activo',
                    ]);
                }
            }
        });
    }
}
