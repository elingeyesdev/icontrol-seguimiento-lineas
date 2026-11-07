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
        Schema::create('micro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propietario_id')->constrained('propietarios')->onDelete('cascade');
            $table->foreignId('interno_id')->nullable()->constrained('interno')->onDelete('set null');
            $table->string('placa', 20);
            $table->string('chasis', 50)->nullable();
            $table->year('anio_fabricacion')->nullable();
            $table->string('modelo', 30);
            $table->string('marca', 30);
            $table->integer('capacidad_pasajeros');
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('micro');
    }
};
