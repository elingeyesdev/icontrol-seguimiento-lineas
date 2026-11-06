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
        Schema::create('asignacion_turnos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('turno_id')->constrained('turno')->onDelete('cascade');
            $table->foreignId('ruta_id')->constrained('ruta')->onDelete('cascade');
            $table->foreignId('micro_id')->constrained('micro')->onDelete('cascade');
            $table->foreignId('conductor_id')->constrained('conductor')->onDelete('cascade');
            $table->date('fecha');
            $table->time('hora_salida')->nullable();
            $table->time('hora_llegada')->nullable();
            $table->enum('estado', ['pendiente', 'en_curso', 'completado', 'retrasado', 'cancelado'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacion_turnos');
    }
};
