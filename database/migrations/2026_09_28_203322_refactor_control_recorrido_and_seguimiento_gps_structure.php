<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Eliminar cualquier tabla detalle_control_recorrido remanente
        Schema::dropIfExists('detalle_control_recorrido');

        // 2. Modificar control_recorrido: agregar asignacion_turno_id y desvincular seguimiento_gps_id
        if (Schema::hasColumn('control_recorrido', 'seguimiento_gps_id')) {
            Schema::table('control_recorrido', function (Blueprint $table) {
                try {
                    $table->dropForeign(['seguimiento_gps_id']);
                } catch (Throwable) {
                }
            });

            Schema::table('control_recorrido', function (Blueprint $table) {
                try {
                    $table->dropIndex(['seguimiento_gps_id', 'estado']);
                } catch (Throwable) {
                }
                $table->dropColumn('seguimiento_gps_id');
            });
        }

        Schema::table('control_recorrido', function (Blueprint $table) {
            if (! Schema::hasColumn('control_recorrido', 'asignacion_turno_id')) {
                $table->foreignId('asignacion_turno_id')
                    ->after('id')
                    ->constrained('asignacion_turnos')
                    ->cascadeOnDelete();
            }

            if (! Schema::hasColumn('control_recorrido', 'ruta_parada_id')) {
                $table->foreignId('ruta_parada_id')
                    ->nullable()
                    ->after('asignacion_turno_id')
                    ->constrained('parada_ruta')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('control_recorrido', 'distancia_metros')) {
                $table->decimal('distancia_metros', 8, 2)->nullable()->after('estado');
            }
        });

        // 3. Modificar seguimiento_gps: desvincular asignacion_turno_id y conectar exclusivamente a control_recorrido_id
        Schema::table('seguimiento_gps', function (Blueprint $table) {
            if (! Schema::hasColumn('seguimiento_gps', 'control_recorrido_id')) {
                $table->foreignId('control_recorrido_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('control_recorrido')
                    ->cascadeOnDelete();
            }
        });

        if (Schema::hasColumn('seguimiento_gps', 'asignacion_turno_id')) {
            Schema::table('seguimiento_gps', function (Blueprint $table) {
                try {
                    $table->dropForeign(['asignacion_turno_id']);
                } catch (Throwable) {
                }
            });

            Schema::table('seguimiento_gps', function (Blueprint $table) {
                try {
                    $table->dropUnique('uk_asignacion_fecha_hora_gps');
                } catch (Throwable) {
                }
                try {
                    $table->dropIndex(['asignacion_turno_id', 'created_at']);
                } catch (Throwable) {
                }
                $table->dropColumn('asignacion_turno_id');
            });
        }

        Schema::table('seguimiento_gps', function (Blueprint $table) {
            try {
                $table->unique(['control_recorrido_id', 'fecha_hora_gps'], 'uk_control_recorrido_fecha_hora_gps');
            } catch (Throwable) {
            }
            try {
                $table->index(['control_recorrido_id', 'created_at'], 'idx_control_recorrido_created_at');
            } catch (Throwable) {
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seguimiento_gps', function (Blueprint $table) {
            if (! Schema::hasColumn('seguimiento_gps', 'asignacion_turno_id')) {
                $table->foreignId('asignacion_turno_id')
                    ->nullable()
                    ->constrained('asignacion_turnos')
                    ->cascadeOnDelete();
            }

            try {
                $table->dropUnique('uk_control_recorrido_fecha_hora_gps');
            } catch (Throwable) {
            }
            try {
                $table->dropIndex('idx_control_recorrido_created_at');
            } catch (Throwable) {
            }

            if (Schema::hasColumn('seguimiento_gps', 'control_recorrido_id')) {
                try {
                    $table->dropForeign(['control_recorrido_id']);
                } catch (Throwable) {
                }
                $table->dropColumn('control_recorrido_id');
            }
        });

        Schema::table('control_recorrido', function (Blueprint $table) {
            if (Schema::hasColumn('control_recorrido', 'asignacion_turno_id')) {
                try {
                    $table->dropForeign(['asignacion_turno_id']);
                } catch (Throwable) {
                }
                $table->dropColumn('asignacion_turno_id');
            }
        });
    }
};
