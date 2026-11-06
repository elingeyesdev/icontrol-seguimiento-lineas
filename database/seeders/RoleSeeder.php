<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $fiscalizador = Role::firstOrCreate(['name' => 'fiscalizador']);
        $conductor = Role::firstOrCreate(['name' => 'conductor']);
        $propietario = Role::firstOrCreate(['name' => 'propietario']);

        $legacyDueño = Role::where('name', 'dueño')->first();

        if ($legacyDueño && $legacyDueño->id !== $propietario->id) {
            $propietario->syncPermissions(array_merge(
                $propietario->permissions()->pluck('name')->all(),
                $legacyDueño->permissions()->pluck('name')->all()
            ));

            foreach ($legacyDueño->users as $user) {
                $user->assignRole($propietario);
            }

            $legacyDueño->delete();
        }

        $permissions = [
            'conductor.index', 'conductor.create', 'conductor.edit', 'conductor.destroy', 'conductor.show',
            'micro.index', 'micro.create', 'micro.edit', 'micro.destroy', 'micro.show',
            'ruta.index', 'ruta.create', 'ruta.edit', 'ruta.destroy', 'ruta.show',
            'parada.index', 'parada.create', 'parada.edit', 'parada.destroy', 'parada.show',
            'turno.index', 'turno.create', 'turno.edit', 'turno.destroy', 'turno.show',
            'asignacion-turno.index', 'asignacion-turno.create', 'asignacion-turno.edit', 'asignacion-turno.destroy', 'asignacion-turno.show',
            'monitoreo.index', 'seguimiento-rutas.index', 'dashboard.view',
            'roles.index', 'roles.create', 'roles.edit', 'roles.destroy', 'users.index', 'users.create', 'users.edit', 'users.destroy',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        $admin->syncPermissions(Permission::all());

        $fiscalizador->syncPermissions([
            'dashboard.view',
            'monitoreo.index', 'seguimiento-rutas.index',
            'asignacion-turno.index', 'asignacion-turno.create', 'asignacion-turno.edit', 'asignacion-turno.show',
            'ruta.index', 'ruta.show', 'turno.index', 'turno.show',
            'micro.index', 'micro.show', 'parada.index', 'parada.show', 'conductor.index', 'conductor.show',
        ]);

        $conductor->syncPermissions([
            'dashboard.view',
            'monitoreo.index', 'seguimiento-rutas.index',
            'asignacion-turno.index', 'asignacion-turno.show', 'turno.index', 'turno.show',
        ]);

        $propietario->syncPermissions([
            'dashboard.view',
            'monitoreo.index', 'seguimiento-rutas.index',
            'conductor.index', 'conductor.create', 'conductor.edit', 'conductor.destroy', 'conductor.show',
            'micro.index', 'micro.create', 'micro.edit', 'micro.destroy', 'micro.show',
            'ruta.index', 'ruta.create', 'ruta.edit', 'ruta.destroy', 'ruta.show',
            'parada.index', 'parada.create', 'parada.edit', 'parada.destroy', 'parada.show',
            'turno.index', 'turno.create', 'turno.edit', 'turno.destroy', 'turno.show',
        ]);
    }
}
