<?php

namespace Database\Factories;

use App\Models\Ruta;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ruta>
 */
class RutaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->numerify('Ruta ##'),
            'descripcion' => fake()->sentence(),
            'estado' => 'activo',
        ];
    }

    public function linea61Azul(): static
    {
        return $this->state([
            'nombre' => '61 (Azul)',
            'descripcion' => implode(' | ', [
                'Microcruz / Mi Micro Santa Cruz',
                'Ida: Urb. El Retoño (Pampa de la Isla) a Ex-Terminal (La Ramada)',
                'Vuelta: Ex-Terminal (La Ramada) a Urb. El Retoño',
                'Ida: Av. Virgen de Luján, Carretera a Cotoca, Cruce del Trillo, Av. Genecherú, Av. 16 de Julio, Av. Cumavi, Feria Cumavi, Av. Tres Pasos al Frente, Av. Intermodal, Av. Brasil, Primer Anillo, Av. Grigotá',
                'Vuelta: Av. Grigotá, Primer Anillo, Av. Brasil, Segundo Anillo, Tercer Anillo, Av. Tres Pasos al Frente, Av. Intermodal, Av. Cumavi, Av. 16 de Julio, Av. Genecherú, Cruce del Trillo, Carretera a Cotoca, Av. Virgen de Luján',
            ]),
            'estado' => 'activo',
        ]);
    }
}
