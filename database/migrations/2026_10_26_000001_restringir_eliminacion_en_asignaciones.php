<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Reemplaza las claves foráneas en cascada de las entidades ligadas a las
 * asignaciones de turno por reglas RESTRICT.
 *
 * Motivo: al eliminar físicamente un conductor, micro, ruta, turno o
 * propietario, MySQL borraba en cascada las asignaciones y, con ellas, sus
 * puntos GPS y controles de recorrido. El historial de operación nunca debe
 * destruirse por una baja de catálogo.
 *
 * SQLite (entorno de pruebas) no permite alterar claves foráneas existentes,
 * por lo que la migración se omite en ese motor. La protección de negocio se
 * valida en IntegridadAsignacionesService y sus pruebas funcionales; la
 * restricción de base de datos se comprueba en MySQL/MariaDB.
 */
return new class extends Migration
{
    /**
     * Relaciones que no deben borrar historia en cascada.
     *
     * @var array<string, array<string, string>>
     */
    private const RESTRICCIONES = [
        'asignacion_turnos' => [
            'turno_id' => 'turno',
            'ruta_id' => 'ruta',
            'micro_id' => 'micro',
            'conductor_id' => 'conductor',
        ],
        'control_recorrido' => [
            'asignacion_turno_id' => 'asignacion_turnos',
        ],
        'seguimiento_gps' => [
            'control_recorrido_id' => 'control_recorrido',
        ],
        'micro' => [
            'propietario_id' => 'propietarios',
        ],
    ];

    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        $this->reescribir('restrict');
    }

    /**
     * Revierte a las reglas originales (cascade) para poder deshacer la
     * migración sin dejar el esquema inconsistente.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        $this->reescribir('cascade');
    }

    private function reescribir(string $accion): void
    {
        foreach (self::RESTRICCIONES as $tabla => $columnas) {
            foreach ($columnas as $columna => $tablaPadre) {
                $this->reemplazarClaveForanea($tabla, $columna, $tablaPadre, $accion);
            }
        }
    }

    private function reemplazarClaveForanea(string $tabla, string $columna, string $tablaPadre, string $accion): void
    {
        if (
            ! Schema::hasTable($tabla)
            || ! Schema::hasColumn($tabla, $columna)
            || ! Schema::hasTable($tablaPadre)
        ) {
            return;
        }

        foreach (Schema::getForeignKeys($tabla) as $claveForanea) {
            if (! in_array($columna, $claveForanea['columns'], true)) {
                continue;
            }

            Schema::table($tabla, function (Blueprint $table) use ($claveForanea) {
                $table->dropForeign($claveForanea['name']);
            });
        }

        Schema::table($tabla, function (Blueprint $table) use ($columna, $tablaPadre, $accion) {
            $nueva = $table->foreign($columna)->references('id')->on($tablaPadre);

            if ($accion === 'restrict') {
                $nueva->restrictOnDelete();
            } else {
                $nueva->cascadeOnDelete();
            }
        });
    }
};
