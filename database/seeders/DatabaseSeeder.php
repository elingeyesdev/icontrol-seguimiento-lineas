<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutar RoleSeeder para crear roles y permisos
        $this->command->info('Configurando roles y permisos...');
        $this->call(RoleSeeder::class);

        // Crear usuario admin
        $admin = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // Asignar rol admin
        $admin->assignRole('admin');
        $this->command->info('Usuario Administrador verificado/creado.');

        // User::factory(10)->create();

        User::firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'password' => bcrypt('password'),
        ]);
        $this->command->info('Usuario Test verificado/creado.');
    }
}
