<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('apellido', 50)->nullable()->after('name');
            $table->string('telefono', 20)->nullable()->after('email');
            $table->string('ci', 20)->nullable()->unique()->after('telefono');
        });
        Schema::table('conductor', function (Blueprint $table) {
            $table->string('licencia', 30)->nullable()->after('user_id');
            $table->string('nombre', 30)->nullable()->change();
            $table->string('apellido', 30)->nullable()->change();
            $table->string('telefono', 15)->nullable()->change();
            $table->string('correo', 50)->nullable()->change();
            $table->string('ci', 20)->nullable()->change();
        });
        Schema::table('propietarios', function (Blueprint $table) {
            $table->string('nombre', 50)->nullable()->change();
            $table->string('apellido', 50)->nullable()->change();
            $table->string('correo', 100)->nullable()->change();
            $table->string('ci', 20)->nullable()->change();
        });

        if (DB::getDriverName() === 'mysql') {
            foreach (['conductor', 'propietarios'] as $tabla) {
                DB::statement("UPDATE users u JOIN {$tabla} p ON p.user_id = u.id SET u.apellido = COALESCE(u.apellido, p.apellido), u.telefono = COALESCE(u.telefono, p.telefono), u.ci = COALESCE(u.ci, p.ci) WHERE p.user_id IS NOT NULL");
            }
        }
    }

    public function down(): void
    {
        Schema::table('conductor', fn (Blueprint $table) => $table->dropColumn('licencia'));
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['ci']);
            $table->dropColumn(['apellido', 'telefono', 'ci']);
        });
    }
};
