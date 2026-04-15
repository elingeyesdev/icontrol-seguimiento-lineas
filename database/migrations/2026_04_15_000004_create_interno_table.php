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
        Schema::create('interno', function (Blueprint $table) {
            $table->id();
            $estado_interno = ['activo', 'inactivo'];
            $table->enum('estado', $estado_interno)->default('inactivo');
            $table->foreignId('micro_id')->constrained('micro'); 
            $table->foreignId('conductor_id')->constrained('conductor');
            $table->datetime('fecha_ingreso');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interno');
    }
};
