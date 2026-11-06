<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TransporteSeeder extends Seeder
{
    /**
     * Compatibilidad con el nombre anterior del seeder.
     */
    public function run(): void
    {
        $this->call(ParametrizacionSeeder::class);
    }
}
