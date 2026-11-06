<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mueve `sentido` de la tabla `ruta` a `parada_ruta`.
     *
     * Pasos:
     *  1. Normaliza los valores de parada_ruta.sentido a capitalización consistente (Ida/Vuelta).
     *     Estrategia: VARCHAR temporal → normalizar datos → ENUM definitivo.
     *     (MySQL no permite ENUM con valores duplicados case-insensitive como 'ida'/'Ida').
     *  2. Agrega índice único (ruta_id, parada_id, sentido) para permitir que una misma parada
     *     exista una vez por sentido en la misma ruta, pero nunca duplicada en el mismo sentido.
     *  3. Elimina la columna `sentido` de la tabla `ruta`.
     */
    public function up(): void
    {
        // ── 1. Normalizar parada_ruta.sentido ──────────────────────────────
        if (Schema::hasTable('parada_ruta') && Schema::hasColumn('parada_ruta', 'sentido')) {
            if ($this->usingMysql()) {
                // Paso 1a: convertir a VARCHAR para poder actualizar valores libremente
                DB::statement("ALTER TABLE `parada_ruta` MODIFY `sentido` VARCHAR(10) NOT NULL DEFAULT 'Ida'");
                // Paso 1b: normalizar capitalización
                DB::table('parada_ruta')->whereRaw("BINARY sentido = 'ida'")->update(['sentido' => 'Ida']);
                DB::table('parada_ruta')->whereRaw("BINARY sentido = 'vuelta'")->update(['sentido' => 'Vuelta']);
                // Paso 1c: volver a ENUM con valores canónicos
                DB::statement("ALTER TABLE `parada_ruta` MODIFY `sentido` ENUM('Ida','Vuelta') NOT NULL DEFAULT 'Ida'");
            }
        }

        // ── 2. Agregar UNIQUE(ruta_id, parada_id, sentido) ─────────────────
        // Una misma parada puede aparecer en Ida Y en Vuelta de la misma ruta,
        // pero no dos veces dentro del mismo sentido.
        if (Schema::hasTable('parada_ruta')) {
            if (! $this->uniqueIndexExists('parada_ruta', 'parada_ruta_ruta_parada_sentido_unique')) {
                Schema::table('parada_ruta', function (Blueprint $table) {
                    $table->unique(['ruta_id', 'parada_id', 'sentido'], 'parada_ruta_ruta_parada_sentido_unique');
                });
            }
        }

        // ── 3. Eliminar sentido de la tabla ruta ───────────────────────────
        if (Schema::hasTable('ruta') && Schema::hasColumn('ruta', 'sentido')) {
            Schema::table('ruta', function (Blueprint $table) {
                $table->dropColumn('sentido');
            });
        }
    }

    /**
     * Revierte la migración: restaura sentido en ruta y elimina el UNIQUE compuesto.
     * Los valores normalizados en parada_ruta NO se revierten (operación segura).
     */
    public function down(): void
    {
        // Restaurar sentido en ruta
        if (Schema::hasTable('ruta') && ! Schema::hasColumn('ruta', 'sentido')) {
            Schema::table('ruta', function (Blueprint $table) {
                $table->enum('sentido', ['Ida', 'Vuelta'])->default('Ida')->after('descripcion');
            });
        }

        // Eliminar UNIQUE compuesto
        if (Schema::hasTable('parada_ruta')) {
            if ($this->uniqueIndexExists('parada_ruta', 'parada_ruta_ruta_parada_sentido_unique')) {
                Schema::table('parada_ruta', function (Blueprint $table) {
                    $table->dropUnique('parada_ruta_ruta_parada_sentido_unique');
                });
            }
        }
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    private function usingMysql(): bool
    {
        return DB::getDriverName() === 'mysql';
    }

    private function uniqueIndexExists(string $table, string $indexName): bool
    {
        if (! $this->usingMysql()) {
            return false;
        }

        $result = DB::selectOne(
            'SELECT COUNT(*) AS cnt FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND INDEX_NAME = ?',
            [$table, $indexName]
        );

        return $result && $result->cnt > 0;
    }
};
