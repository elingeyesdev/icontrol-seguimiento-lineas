<?php

namespace Database\Seeders;

use App\Models\AsignacionTurno;
use App\Models\Conductor;
use App\Models\Micro;
use App\Models\Ruta;
use App\Models\Turno;
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
        $this->call(RoleSeeder::class);

        // Catálogos base: parametricación, transporte y turnos del día.
        $this->call(ParametrizacionSeeder::class);
        $this->call(Linea61Seeder::class);
        $this->call(TransporteSeeder::class);
        $this->call(TurnoSeeder::class);

        // ─── Usuarios de demostración ───────────────────────────────────────
        $admin = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Administrador',
            'apellido' => 'Sistema',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Conductor: sin este usuario el flujo operativo de la app móvil es
        // inalcanzable, porque AsignacionTurnoApi resuelve el conductor a través
        // de $request->user()->conductor y devuelve 403 si no existe.
        $conductorUser = User::firstOrCreate([
            'email' => 'conductor@linea61.test',
        ], [
            'name' => 'Miguel',
            'apellido' => 'Quispe',
            'email' => 'conductor@linea61.test',
            'telefono' => '70110001',
            'ci' => '8200001',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $conductorUser->assignRole('conductor');

        // Vincular el perfil Conductor sembrado con el usuario.
        $conductor = Conductor::where('correo', 'miguel.quispe@linea61.test')->first()
            ?? Conductor::where('user_id', $conductorUser->id)->first();

        if ($conductor) {
            $conductor->update(['user_id' => $conductorUser->id, 'estado' => 'activo']);
        }

        // Turno del día para que la app muestre un turno al conductor al entrar.
        $turno = Turno::where('estado', 'activo')
            ->orderByRaw("FIELD(nombre, 'mañana', 'tarde', 'noche')")
            ->first();

        $ruta = Ruta::where('estado', 'activo')->first();
        $micro = Micro::where('estado', 'activo')->first();

        if ($turno && $ruta && $micro && $conductor) {
            AsignacionTurno::firstOrCreate(
                [
                    'conductor_id' => $conductor->id,
                    'turno_id' => $turno->id,
                    'fecha' => now()->toDateString(),
                ],
                [
                    'ruta_id' => $ruta->id,
                    'micro_id' => $micro->id,
                    'estado' => 'pendiente',
                    'observaciones' => 'Asignación de demostración para la app móvil.',
                ]
            );
        }

        User::firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
    }
}
