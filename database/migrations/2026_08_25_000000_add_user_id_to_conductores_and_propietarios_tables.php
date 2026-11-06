<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Vincula las fichas operativas con las cuentas del módulo de seguridad.
     * Un usuario puede tener ambas fichas, pero solo una de cada tipo.
     */
    public function up(): void
    {
        Schema::table('conductor', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->unique()
                ->constrained()
                ->nullOnDelete()
                ->after('id');
        });

        Schema::table('propietarios', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->unique()
                ->constrained()
                ->nullOnDelete()
                ->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('conductor', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });

        Schema::table('propietarios', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
