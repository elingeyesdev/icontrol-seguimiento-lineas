<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Asegura que asignacion_turnos tenga todos los campos operativos requeridos.
 * Esta migración es idempotente: solo agrega lo que falta.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('asignacion_turnos')) {
            return;
        }

        Schema::table('asignacion_turnos', function (Blueprint $table) {
            if (! Schema::hasColumn('asignacion_turnos', 'interno_id')) {
                $table->foreignId('interno_id')->nullable()->after('micro_id')
                    ->constrained('interno')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        // No-op
    }
};
