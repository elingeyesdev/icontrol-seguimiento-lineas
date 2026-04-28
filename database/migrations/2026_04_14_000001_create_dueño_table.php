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
       Schema::create('dueño', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 30);
            $table->string('apellido', 30);
            $table->string('telefono', 15);
            $table->string('correo', 50)->unique();
            $table->string('ci', 20)->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
