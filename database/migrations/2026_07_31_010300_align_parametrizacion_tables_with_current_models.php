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
        $this->alignConductor();
        $this->alignPropietarios();
        $this->alignInterno();
        $this->alignMicro();
        $this->alignParadas();
        $this->alignParadaRuta();
        $this->alignTurno();
    }

    private function alignConductor(): void
    {
        if (! Schema::hasTable('conductor') || Schema::hasColumn('conductor', 'estado')) {
            return;
        }

        Schema::table('conductor', function (Blueprint $table) {
            $table->enum('estado', ['activo', 'inactivo'])->default('activo')->after('ci');
        });
    }

    private function alignPropietarios(): void
    {
        if (! Schema::hasTable('propietarios')) {
            return;
        }

        $this->addColumnIfMissing('propietarios', 'nombre', function (Blueprint $table) {
            $table->string('nombre', 50)->nullable()->after('id');
        });

        $this->addColumnIfMissing('propietarios', 'apellido', function (Blueprint $table) {
            $table->string('apellido', 50)->nullable()->after('nombre');
        });

        $this->addColumnIfMissing('propietarios', 'telefono', function (Blueprint $table) {
            $table->string('telefono', 20)->nullable()->after('apellido');
        });

        $this->addColumnIfMissing('propietarios', 'correo', function (Blueprint $table) {
            $table->string('correo', 100)->nullable()->after('telefono');
        });

        $this->addColumnIfMissing('propietarios', 'ci', function (Blueprint $table) {
            $table->string('ci', 20)->nullable()->after('correo');
        });

        $this->addColumnIfMissing('propietarios', 'estado', function (Blueprint $table) {
            $table->enum('estado', ['activo', 'inactivo'])->default('activo')->after('ci');
        });

        $this->addColumnIfMissing('propietarios', 'fecha_registro', function (Blueprint $table) {
            $table->date('fecha_registro')->nullable()->after('estado');
        });
    }

    private function alignInterno(): void
    {
        if (! Schema::hasTable('interno')) {
            return;
        }

        if (! Schema::hasColumn('interno', 'estado')) {
            Schema::table('interno', function (Blueprint $table) {
                $table->enum('estado', ['disponible', 'asignado', 'inactivo'])->default('disponible')->after('id');
            });

            return;
        }

        if (! $this->usingMysql()) {
            return;
        }

        if ($this->columnType('interno', 'estado') !== "enum('disponible','asignado','inactivo')") {
            DB::statement("ALTER TABLE `interno` MODIFY `estado` ENUM('activo','disponible','asignado','inactivo') NOT NULL DEFAULT 'disponible'");
            DB::table('interno')->where('estado', 'activo')->update(['estado' => 'disponible']);
            DB::statement("ALTER TABLE `interno` MODIFY `estado` ENUM('disponible','asignado','inactivo') NOT NULL DEFAULT 'disponible'");
        }
    }

    private function alignMicro(): void
    {
        if (! Schema::hasTable('micro')) {
            return;
        }

        if (! Schema::hasColumn('micro', 'estado')) {
            Schema::table('micro', function (Blueprint $table) {
                $table->enum('estado', ['activo', 'inactivo'])->default('activo')->after('capacidad_pasajeros');
            });

            return;
        }

        if (! $this->usingMysql()) {
            return;
        }

        if ($this->columnType('micro', 'estado') !== "enum('activo','inactivo')") {
            DB::statement("ALTER TABLE `micro` MODIFY `estado` ENUM('activo','en_taller','baja','inactivo') NOT NULL DEFAULT 'activo'");
            DB::table('micro')->whereIn('estado', ['en_taller', 'baja'])->update(['estado' => 'inactivo']);
            DB::statement("ALTER TABLE `micro` MODIFY `estado` ENUM('activo','inactivo') NOT NULL DEFAULT 'activo'");
        }
    }

    private function alignParadas(): void
    {
        if (! Schema::hasTable('paradas')) {
            return;
        }

        $this->addColumnIfMissing('paradas', 'referencia', function (Blueprint $table) {
            $table->string('referencia', 255)->nullable()->after('nombre');
        });

        $this->addColumnIfMissing('paradas', 'estado', function (Blueprint $table) {
            $table->enum('estado', ['activo', 'inactivo'])->default('activo')->after('longitud');
        });
    }

    private function alignParadaRuta(): void
    {
        if (! Schema::hasTable('parada_ruta')) {
            return;
        }

        $this->addColumnIfMissing('parada_ruta', 'sentido', function (Blueprint $table) {
            $table->enum('sentido', ['Ida', 'Vuelta'])->default('Ida')->after('orden');
        });

        $this->addColumnIfMissing('parada_ruta', 'estado', function (Blueprint $table) {
            $table->enum('estado', ['activo', 'inactivo'])->default('activo')->after('sentido');
        });
    }

    private function alignTurno(): void
    {
        if (! Schema::hasTable('turno')) {
            return;
        }

        $this->addColumnIfMissing('turno', 'interno_id', function (Blueprint $table) {
            $table->foreignId('interno_id')->nullable()->after('id')->constrained('interno')->nullOnDelete();
        });

        $this->addColumnIfMissing('turno', 'ruta_id', function (Blueprint $table) {
            $table->foreignId('ruta_id')->nullable()->after('interno_id')->constrained('ruta')->nullOnDelete();
        });

        $this->addColumnIfMissing('turno', 'fecha_laboral', function (Blueprint $table) {
            $table->date('fecha_laboral')->nullable()->after('hora_fin');
        });

        if ($this->usingMysql() && Schema::hasColumn('turno', 'tipo')) {
            DB::statement('ALTER TABLE `turno` MODIFY `tipo` VARCHAR(20) NULL');
        }
    }

    private function addColumnIfMissing(string $tableName, string $columnName, callable $callback): void
    {
        if (Schema::hasColumn($tableName, $columnName)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($callback) {
            $callback($table);
        });
    }

    private function columnType(string $tableName, string $columnName): ?string
    {
        $column = DB::selectOne(
            'SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            [$tableName, $columnName],
        );

        return $column?->COLUMN_TYPE;
    }

    private function usingMysql(): bool
    {
        return DB::getDriverName() === 'mysql';
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: this migration only brings already-created tables in line with
        // the current models and keeps existing data compatible.
    }
};
