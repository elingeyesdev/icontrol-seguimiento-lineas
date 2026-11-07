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
        // Conductor Interno pivot table (N-M relationship)
        Schema::create('conductor_interno', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conductor_id')->constrained('conductor')->onDelete('cascade');
            $table->foreignId('interno_id')->constrained('interno')->onDelete('cascade');
            $table->timestamps();
        });

        // Interno Turno pivot table (N-M relationship)
        Schema::create('interno_turno', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interno_id')->constrained('interno')->onDelete('cascade');
            $table->foreignId('turno_id')->constrained('turno')->onDelete('cascade');
            $table->timestamps();
        });

        // Parada Ruta pivot table (N-M relationship)
        Schema::create('parada_ruta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parada_id')->constrained('paradas')->onDelete('cascade');
            $table->foreignId('ruta_id')->constrained('ruta')->onDelete('cascade');
            $table->timestamps();
        });

        // Asigna relationship (Ternary table: Turno [1], Interno [M], Horario [N])
        Schema::create('asigna', function (Blueprint $table) {
            $table->id();
            $table->foreignId('turno_id')->constrained('turno')->onDelete('cascade');
            $table->foreignId('interno_id')->constrained('interno')->onDelete('cascade');
            $table->foreignId('horario_id')->constrained('horario')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asigna');
        Schema::dropIfExists('parada_ruta');
        Schema::dropIfExists('interno_turno');
        Schema::dropIfExists('conductor_interno');
    }
};
