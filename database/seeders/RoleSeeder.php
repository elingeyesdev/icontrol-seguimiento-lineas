<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $conductor = Role::firstOrCreate(['name' => 'conductor']);
        $dueño = Role::firstOrCreate(['name' => 'dueño']);

        // Crear permisos para conductores
        Permission::firstOrCreate(['name' => 'conductor.index']);
        Permission::firstOrCreate(['name' => 'conductor.create']);
        Permission::firstOrCreate(['name' => 'conductor.edit']);
        Permission::firstOrCreate(['name' => 'conductor.destroy']);
        Permission::firstOrCreate(['name' => 'conductor.show']);

        // Crear permisos para micros
        Permission::firstOrCreate(['name' => 'micro.index']);
        Permission::firstOrCreate(['name' => 'micro.create']);
        Permission::firstOrCreate(['name' => 'micro.edit']);
        Permission::firstOrCreate(['name' => 'micro.destroy']);
        Permission::firstOrCreate(['name' => 'micro.show']);

        // Crear permisos para rutas
        Permission::firstOrCreate(['name' => 'ruta.index']);
        Permission::firstOrCreate(['name' => 'ruta.create']);
        Permission::firstOrCreate(['name' => 'ruta.edit']);
        Permission::firstOrCreate(['name' => 'ruta.destroy']);
        Permission::firstOrCreate(['name' => 'ruta.show']);

        // Crear permisos para turnos
        Permission::firstOrCreate(['name' => 'turno.index']);
        Permission::firstOrCreate(['name' => 'turno.create']);
        Permission::firstOrCreate(['name' => 'turno.edit']);
        Permission::firstOrCreate(['name' => 'turno.destroy']);
        Permission::firstOrCreate(['name' => 'turno.show']);

        // Asignar permisos a roles
        // Admin tiene todos los permisos
        $admin->syncPermissions(Permission::all());

        // Conductor: solo ver conductores y turnos (index, show), no puede eliminar turnos
        $conductor->syncPermissions([
            'conductor.index', 'conductor.show',
            'turno.index', 'turno.show'
        ]);

        // Dueño: permisos para conductores y micros (index, create, edit, show), rutas (index, create, edit, show)
        $dueño->syncPermissions([
            'conductor.index', 'conductor.create', 'conductor.edit', 'conductor.show',
            'micro.index', 'micro.create', 'micro.edit', 'micro.show',
            'ruta.index', 'ruta.create', 'ruta.edit', 'ruta.show'
        ]);
    }
}
