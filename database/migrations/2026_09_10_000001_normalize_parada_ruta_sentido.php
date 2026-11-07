<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('parada_ruta') || ! Schema::hasColumn('parada_ruta', 'sentido')) {
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
            DB::statement('ALTER TABLE parada_ruta RENAME TO parada_ruta_legacy');

            Schema::create('parada_ruta', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ruta_id')->constrained('ruta')->onDelete('cascade');
                $table->foreignId('parada_id')->constrained('paradas')->onDelete('cascade');
                $table->integer('orden');
                $table->enum('sentido', ['Ida', 'Vuelta'])->default('Ida');
                $table->enum('estado', ['activo', 'inactivo'])->default('activo');
                $table->timestamps();
            });

            DB::statement("INSERT INTO parada_ruta (id, ruta_id, parada_id, orden, sentido, estado, created_at, updated_at)
                SELECT id, ruta_id, parada_id, orden,
                    CASE LOWER(sentido) WHEN 'vuelta' THEN 'Vuelta' ELSE 'Ida' END,
                    estado, created_at, updated_at
                FROM parada_ruta_legacy");
            DB::statement('DROP TABLE parada_ruta_legacy');
            DB::statement('CREATE UNIQUE INDEX parada_ruta_ruta_parada_sentido_unique ON parada_ruta (ruta_id, parada_id, sentido)');

            if (Schema::hasTable('control_recorrido')) {
                DB::statement('ALTER TABLE control_recorrido RENAME TO control_recorrido_legacy');

                Schema::create('control_recorrido', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('seguimiento_gps_id')->constrained('seguimiento_gps')->onDelete('cascade');
                    $table->foreignId('ruta_parada_id')->nullable()->constrained('parada_ruta')->onDelete('set null');
                    $table->dateTime('fecha_hora');
                    $table->enum('estado', ['pendiente', 'cumplido', 'omitido', 'fuera_ruta'])->default('pendiente');
                    $table->decimal('distancia_metros', 8, 2)->nullable();
                    $table->text('observacion')->nullable();
                    $table->timestamps();
                });

                DB::statement('INSERT INTO control_recorrido (id, seguimiento_gps_id, ruta_parada_id, fecha_hora, estado, distancia_metros, observacion, created_at, updated_at)
                    SELECT id, seguimiento_gps_id, ruta_parada_id, fecha_hora, estado, distancia_metros, observacion, created_at, updated_at
                    FROM control_recorrido_legacy');
                DB::statement('DROP TABLE control_recorrido_legacy');
                DB::statement('CREATE INDEX control_recorrido_seguimiento_gps_id_estado_index ON control_recorrido (seguimiento_gps_id, estado)');
            }

            DB::statement('PRAGMA foreign_keys = ON');
        }
    }

    public function down(): void
    {
        // The canonical values are retained on rollback to avoid corrupting history.
    }
};
