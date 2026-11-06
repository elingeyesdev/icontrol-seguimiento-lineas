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
        if (! Schema::hasTable('ruta')) {
            return;
        }

        $hasNombre = Schema::hasColumn('ruta', 'nombre');
        $hasNombreRuta = Schema::hasColumn('ruta', 'nombre_ruta');
        $hasOrigen = Schema::hasColumn('ruta', 'origen');
        $hasDestino = Schema::hasColumn('ruta', 'destino');
        $hasDescripcion = Schema::hasColumn('ruta', 'descripcion');
        $hasSentido = Schema::hasColumn('ruta', 'sentido');
        $hasEstado = Schema::hasColumn('ruta', 'estado');

        Schema::table('ruta', function (Blueprint $table) use ($hasNombre, $hasDescripcion, $hasSentido, $hasEstado) {
            if (! $hasNombre) {
                $table->string('nombre', 50)->nullable()->after('id');
            }

            if (! $hasDescripcion) {
                $table->text('descripcion')->nullable()->after('nombre');
            }

            if (! $hasSentido) {
                $table->enum('sentido', ['Ida', 'Vuelta'])->default('Ida')->after('descripcion');
            }

            if (! $hasEstado) {
                $table->enum('estado', ['activo', 'inactivo'])->default('activo')->after('sentido');
            }
        });

        if (! $hasNombre) {
            if ($hasNombreRuta) {
                DB::table('ruta')
                    ->whereNull('nombre')
                    ->update(['nombre' => DB::raw("COALESCE(NULLIF(nombre_ruta, ''), 'Ruta sin nombre')")]);
            } else {
                DB::table('ruta')
                    ->whereNull('nombre')
                    ->update(['nombre' => 'Ruta sin nombre']);
            }
        }

        if (! $hasDescripcion && $hasOrigen && $hasDestino) {
            DB::table('ruta')
                ->whereNull('descripcion')
                ->update(['descripcion' => DB::raw("NULLIF(CONCAT(origen, ' - ', destino), ' - ')")]);
        }

        Schema::table('ruta', function (Blueprint $table) use ($hasOrigen, $hasDestino, $hasNombreRuta) {
            if ($hasOrigen) {
                $table->string('origen', 50)->nullable()->change();
            }

            if ($hasDestino) {
                $table->string('destino', 50)->nullable()->change();
            }

            if ($hasNombreRuta) {
                $table->string('nombre_ruta', 50)->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: this migration preserves legacy columns and only makes the table
        // compatible with the current Ruta model.
    }
};
