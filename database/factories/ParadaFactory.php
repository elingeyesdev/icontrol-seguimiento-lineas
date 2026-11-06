<?php

namespace Database\Factories;

use App\Models\Parada;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Parada>
 */
class ParadaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->streetName(),
            'referencia' => fake()->optional()->sentence(),
            'latitud' => fake()->latitude(-17.85, -17.70),
            'longitud' => fake()->longitude(-63.25, -63.05),
            'estado' => 'activo',
        ];
    }
}
