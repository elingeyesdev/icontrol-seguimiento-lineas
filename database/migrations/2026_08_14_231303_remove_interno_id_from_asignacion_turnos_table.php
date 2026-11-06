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
        if (! Schema::hasTable('asignacion_turnos')) {

            return;
        }
        Schema::table('asignacion_turnos', function (Blueprint $table) {
            if (Schema::hasColumn('asignacion_turnos', 'interno_id')) {
                $table->dropForeign(['interno_id']);
                $table->dropColumn('interno_id');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('asignacion_turnos')) {

            return;
        }
        Schema::table('asignacion_turnos', function (Blueprint $table) {
            if (Schema::hasColumn('asignacion_turnos', 'interno_id')) {
                $table->foreignId('interno_id')
                    ->nullable()
                    ->after('micro_id')
                    ->constrained('interno')
                    ->nullOnDelete();
            }
        });
    }
};
