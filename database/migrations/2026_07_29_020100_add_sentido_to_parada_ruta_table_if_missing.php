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
        if (! Schema::hasTable('parada_ruta') || Schema::hasColumn('parada_ruta', 'sentido')) {
            return;
        }

        Schema::table('parada_ruta', function (Blueprint $table) {
            $table->enum('sentido', ['Ida', 'Vuelta'])->default('Ida')->after('orden');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('parada_ruta') || ! Schema::hasColumn('parada_ruta', 'sentido')) {
            return;
        }

        Schema::table('parada_ruta', function (Blueprint $table) {
            $table->dropColumn('sentido');
        });
    }
};
