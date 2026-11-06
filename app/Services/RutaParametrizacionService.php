<?php

namespace App\Services;

use App\Models\Ruta;
use Illuminate\Support\Facades\DB;

class RutaParametrizacionService
{
    /**
     * Guarda/reemplaza la parametrización completa de una ruta.
     *
     * Elimina todas las filas de `parada_ruta` para los sentidos indicados y las
     * recrea en una sola transacción corta. Permite actualizar Ida, Vuelta o ambos
     * sentidos de forma independiente.
     *
     * @param  array  $paradasIda  Array de parada_id en el orden deseado (sentido Ida).
     *                             Pasar array vacío [] para borrar las paradas de Ida.
     *                             Pasar null para no tocar las paradas de Ida.
     * @param  array  $paradasVuelta  Igual que $paradasIda pero para Vuelta.
     */
    public function guardarParametrizacion(
        Ruta $ruta,
        ?array $paradasIda,
        ?array $paradasVuelta
    ): void {
        DB::transaction(function () use ($ruta, $paradasIda, $paradasVuelta) {
            if ($paradasIda !== null) {
                $this->reemplazarSentido($ruta->id, 'Ida', $paradasIda);
            }

            if ($paradasVuelta !== null) {
                $this->reemplazarSentido($ruta->id, 'Vuelta', $paradasVuelta);
            }
        });
    }

    /**
     * Elimina las paradas de un sentido específico y las recrea.
     *
     * @param  string  $sentido  'Ida' | 'Vuelta'
     * @param  array  $paradaIds  Array de parada_id ordenados
     */
    private function reemplazarSentido(int $rutaId, string $sentido, array $paradaIds): void
    {
        // 1. Eliminar las filas existentes del sentido indicado
        DB::table('parada_ruta')
            ->where('ruta_id', $rutaId)
            ->where('sentido', $sentido)
            ->delete();

        // 2. Sanitizar para evitar duplicados en el mismo sentido
        $uniqueParadaIds = array_values(array_unique(array_filter($paradaIds)));

        // 3. Insertar las nuevas paradas con su orden y sentido
        $rows = [];
        foreach ($uniqueParadaIds as $index => $paradaId) {
            $rows[] = [
                'ruta_id' => $rutaId,
                'parada_id' => (int) $paradaId,
                'orden' => $index + 1,
                'sentido' => $sentido,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (! empty($rows)) {
            DB::table('parada_ruta')->insert($rows);
        }
    }
}
