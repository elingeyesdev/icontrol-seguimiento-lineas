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
        Schema::create('turno', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interno_id')->constrained('interno')->onDelete('cascade');
            $table->foreignId('ruta_id')->constrained('ruta')->onDelete('cascade');
            $table->datetime('hora_inicio');
            $table->datetime('hora_fin');
            $table->date('fecha_laboral');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('turno');
    }
};
