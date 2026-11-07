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
            $table->enum('estado', ['disponible', 'asignado', 'inactivo'])->default('disponible');
            $table->string('numero_interno', 10)->unique();
            $table->datetime('fecha_ingreso');
            $table->text('observaciones')->nullable();
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
