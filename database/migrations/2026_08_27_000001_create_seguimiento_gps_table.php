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
        Schema::create('seguimiento_gps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asignacion_turno_id')->constrained('asignacion_turnos')->onDelete('cascade');
            $table->dateTime('fecha_hora_gps');
            $table->decimal('latitud', 10, 8);
            $table->decimal('longitud', 11, 8);
            $table->decimal('velocidad', 8, 2)->nullable()->default(0.00);
            $table->dateTime('fecha_hora_sincronizacion')->nullable();
            $table->timestamps();

            // Índice único para evitar duplicados en retransmisiones offline (SDD Sección 26.5)
            $table->unique(['asignacion_turno_id', 'fecha_hora_gps'], 'uk_asignacion_fecha_hora_gps');
            $table->index(['asignacion_turno_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seguimiento_gps');
    }
};
