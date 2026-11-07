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
        Schema::create('control_recorrido', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seguimiento_gps_id')->constrained('seguimiento_gps')->onDelete('cascade');
            $table->foreignId('ruta_parada_id')->nullable()->constrained('parada_ruta')->onDelete('set null');
            $table->dateTime('fecha_hora');
            $table->enum('estado', ['pendiente', 'cumplido', 'omitido', 'fuera_ruta'])->default('pendiente');
            $table->decimal('distancia_metros', 8, 2)->nullable();
            $table->text('observacion')->nullable();
            $table->timestamps();

            $table->index(['seguimiento_gps_id', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('control_recorrido');
    }
};
