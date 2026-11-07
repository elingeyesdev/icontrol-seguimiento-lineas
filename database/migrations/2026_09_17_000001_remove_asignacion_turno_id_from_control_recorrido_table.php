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
        if (! Schema::hasTable('control_recorrido')) {
            return;
        }

        Schema::table('control_recorrido', function (Blueprint $table) {
            if (Schema::hasColumn('control_recorrido', 'asignacion_turno_id')) {
                $table->dropForeign(['asignacion_turno_id']);
                $table->dropColumn('asignacion_turno_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('control_recorrido')) {
            return;
        }

        Schema::table('control_recorrido', function (Blueprint $table) {
            if (! Schema::hasColumn('control_recorrido', 'asignacion_turno_id')) {
                $table->foreignId('asignacion_turno_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('asignacion_turnos')
                    ->cascadeOnDelete();
            }
        });
    }
};
