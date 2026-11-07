<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Refactoriza la tabla `turno` para que sea un catálogo estático de horarios.
     *
     * Elimina: fiscalizador_id, interno_id, ruta_id, fecha_laboral, tipo (varchar heredado).
     * Agrega  : nombre (enum mañana/tarde/noche), descripcion, estado.
     */
    public function up(): void
    {
        if (! Schema::hasTable('turno')) {
            return;
        }

        // ---------------------------------------------------------------
        // 1. Eliminar FK a fiscalizadors si existe
        // ---------------------------------------------------------------
        if (Schema::hasColumn('turno', 'fiscalizador_id')) {
            Schema::table('turno', function (Blueprint $table) {
                // Intentar soltar la FK con convención Laravel estándar
                try {
                    $table->dropForeign(['fiscalizador_id']);
                } catch (Throwable $e) {
                    // Si la FK tiene otro nombre, ignorar
                }
                $table->dropColumn('fiscalizador_id');
            });
        }

        // ---------------------------------------------------------------
        // 2. Eliminar FK a interno si existe
        // ---------------------------------------------------------------
        if (Schema::hasColumn('turno', 'interno_id')) {
            Schema::table('turno', function (Blueprint $table) {
                try {
                    $table->dropForeign(['interno_id']);
                } catch (Throwable $e) {
                }
                $table->dropColumn('interno_id');
            });
        }

        // ---------------------------------------------------------------
        // 3. Eliminar FK a ruta si existe
        // ---------------------------------------------------------------
        if (Schema::hasColumn('turno', 'ruta_id')) {
            Schema::table('turno', function (Blueprint $table) {
                try {
                    $table->dropForeign(['ruta_id']);
                } catch (Throwable $e) {
                }
                $table->dropColumn('ruta_id');
            });
        }

        // ---------------------------------------------------------------
        // 4. Eliminar fecha_laboral si existe
        // ---------------------------------------------------------------
        if (Schema::hasColumn('turno', 'fecha_laboral')) {
            Schema::table('turno', function (Blueprint $table) {
                $table->dropColumn('fecha_laboral');
            });
        }

        // ---------------------------------------------------------------
        // 5. Convertir/renombrar columna `tipo` → `nombre` como ENUM
        // ---------------------------------------------------------------
        if (Schema::hasColumn('turno', 'tipo') && ! Schema::hasColumn('turno', 'nombre')) {
            // Renombrar tipo → nombre
            Schema::table('turno', function (Blueprint $table) {
                $table->renameColumn('tipo', 'nombre');
            });
        }

        // Asegurar que `nombre` sea ENUM correcto (mañana/tarde/noche)
        if (Schema::hasColumn('turno', 'nombre') && DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `turno` MODIFY `nombre` ENUM('mañana','tarde','noche') NOT NULL");
        }

        // ---------------------------------------------------------------
        // 6. Agregar descripcion si no existe
        // ---------------------------------------------------------------
        if (! Schema::hasColumn('turno', 'descripcion')) {
            Schema::table('turno', function (Blueprint $table) {
                $table->string('descripcion', 255)->nullable()->after('hora_fin');
            });
        }

        // ---------------------------------------------------------------
        // 7. Agregar estado si no existe
        // ---------------------------------------------------------------
        if (! Schema::hasColumn('turno', 'estado')) {
            Schema::table('turno', function (Blueprint $table) {
                $table->enum('estado', ['activo', 'inactivo'])->default('activo')->after('descripcion');
            });
        }
    }

    /**
     * Reverse the migrations.
     * No se deshace por seguridad de datos históricos.
     */
    public function down(): void
    {
        // No-op: no revertir para no perder datos
    }
};
