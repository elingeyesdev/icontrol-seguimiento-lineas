<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Inserta los tres turnos del catálogo estático (Mañana, Tarde, Noche).
 * Es idempotente: no inserta si ya existen.
 */
class TurnoSeeder extends Seeder
{
    public function run(): void
    {
        $turnos = [
            [
                'nombre' => 'mañana',
                'hora_inicio' => '05:00:00',
                'hora_fin' => '13:00:00',
                'descripcion' => 'Turno de mañana',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'tarde',
                'hora_inicio' => '13:00:00',
                'hora_fin' => '21:00:00',
                'descripcion' => 'Turno de tarde',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'noche',
                'hora_inicio' => '21:00:00',
                'hora_fin' => '05:00:00',
                'descripcion' => 'Turno nocturno',
                'estado' => 'activo',
            ],
        ];

        foreach ($turnos as $turno) {
            DB::table('turno')->updateOrInsert(
                ['nombre' => $turno['nombre']],
                array_merge($turno, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
