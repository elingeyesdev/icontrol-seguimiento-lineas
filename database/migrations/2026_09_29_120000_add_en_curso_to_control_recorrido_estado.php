<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * SeguimientoGpsService::registrarPunto() escribe 'en_curso' en el
     * control_recorrido general, pero el ENUM solo permite
     * pendiente/cumplido/omitido/fuera_ruta. Bajo STRICT_TRANS_TABLES el
     * INSERT es rechazado y toda la transacción de DB::transaction() revierte,
     * por lo que ningun punto GPS se puede registrar desde la app movil.
     */
    public function up(): void
    {
        if (! Schema::hasTable('control_recorrido') || ! Schema::hasColumn('control_recorrido', 'estado')) {
            return;
        }

        $estado = DB::table('control_recorrido')->distinct()->pluck('estado')->all();

        if (in_array('en_curso', $estado, true)) {
            return;
        }

        Schema::table('control_recorrido', function (Blueprint $table) {
            $table->enum('estado', [
                'pendiente',
                'en_curso',
                'cumplido',
                'omitido',
                'fuera_ruta',
            ])->default('pendiente')->change();
        });
    }

    public function down(): void
    {
        DB::table('control_recorrido')->where('estado', 'en_curso')->update(['estado' => 'pendiente']);

        Schema::table('control_recorrido', function (Blueprint $table) {
            $table->enum('estado', ['pendiente', 'cumplido', 'omitido', 'fuera_ruta'])
                ->default('pendiente')
                ->change();
        });
    }
};
