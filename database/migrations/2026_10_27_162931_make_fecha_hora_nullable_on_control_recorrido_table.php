<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('control_recorrido', function (Blueprint $table) {
            $table->dateTime('fecha_hora')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('control_recorrido')
            ->whereNull('fecha_hora')
            ->update(['fecha_hora' => now()]);

        Schema::table('control_recorrido', function (Blueprint $table) {
            $table->dateTime('fecha_hora')->nullable(false)->change();
        });
    }
};
