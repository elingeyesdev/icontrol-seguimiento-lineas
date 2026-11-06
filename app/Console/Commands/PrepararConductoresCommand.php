<?php

namespace App\Console\Commands;

use App\Models\Conductor;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class PrepararConductoresCommand extends Command
{
    protected $signature = 'linea61:preparar-conductores
                            {--password= : Contraseña para las cuentas creadas (por defecto CONDUCTOR_PASSWORD o "password")}';

    protected $description = 'Crea y vincula cuentas de usuario para las fichas de conductor que aún no tienen acceso.';

    public function handle(): int
    {
        $password = $this->option('password')
            ?: env('CONDUCTOR_PASSWORD', 'password');

        if (! Role::where('name', 'conductor')->exists()) {
            $this->error('No existe el rol "conductor". Ejecute primero los seeders de roles.');

            return self::FAILURE;
        }

        $creados = 0;
        $vinculados = 0;
        $omitidos = 0;

        Conductor::query()
            ->whereNull('user_id')
            ->orderBy('id')
            ->get()
            ->each(function (Conductor $conductor) use ($password, &$creados, &$vinculados, &$omitidos) {
                $email = trim((string) $conductor->getRawOriginal('correo'));
                $ci = trim((string) $conductor->getRawOriginal('ci'));

                if ($email === '') {
                    $this->warn("Conductor #{$conductor->id} sin correo: omitido.");
                    $omitidos++;

                    return;
                }

                $user = User::query()
                    ->where('email', $email)
                    ->when($ci !== '', fn ($query) => $query->orWhere('ci', $ci))
                    ->first();

                if (! $user) {
                    $user = User::create([
                        'name' => $conductor->getRawOriginal('nombre') ?: 'Conductor',
                        'apellido' => $conductor->getRawOriginal('apellido') ?: '',
                        'telefono' => $conductor->getRawOriginal('telefono'),
                        'ci' => $ci !== '' ? $ci : null,
                        'email' => $email,
                        'password' => Hash::make($password),
                        'email_verified_at' => now(),
                    ]);
                    $creados++;
                    $this->info("Usuario creado: {$email}");
                }

                $yaVinculado = Conductor::where('user_id', $user->id)
                    ->whereKeyNot($conductor->id)
                    ->exists();

                if ($yaVinculado) {
                    $this->warn("El usuario {$email} ya está vinculado a otro conductor: omitido.");
                    $omitidos++;

                    return;
                }

                $conductor->update(['user_id' => $user->id]);

                if (! $user->hasRole('conductor')) {
                    $user->assignRole('conductor');
                }

                $vinculados++;
                $this->line("Conductor #{$conductor->id} vinculado con {$email}");
            });

        $this->newLine();
        $this->info("Resumen: {$creados} usuarios creados, {$vinculados} conductores vinculados, {$omitidos} omitidos.");
        $this->line('Contraseña utilizada: '.($password === 'password' ? 'password (por defecto)' : '(personalizada)'));

        return self::SUCCESS;
    }
}
