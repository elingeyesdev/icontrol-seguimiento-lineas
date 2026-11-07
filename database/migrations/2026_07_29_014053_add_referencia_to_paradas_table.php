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
        Schema::table('paradas', function (Blueprint $table) {
            // Añadir columna 'referencia' después de 'nombre' si no existe
            if (! Schema::hasColumn('paradas', 'referencia')) {
                $table->string('referencia', 255)->nullable()->after('nombre');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paradas', function (Blueprint $table) {
            $table->dropColumn('referencia');
        });
    }
};
