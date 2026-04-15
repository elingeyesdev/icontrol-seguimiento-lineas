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
            $table->foreignId('dueño_id')->constrained('dueño');
            $table->string('placa', 20);
            $table->string('modelo', 30);
            $table->string('marca', 30);
            $table->integer('capacidad_pasajeros'); 
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
