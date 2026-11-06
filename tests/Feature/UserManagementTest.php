<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_fiscalizador_can_access_user_management(): void
    {
        $role = Role::create(['name' => 'fiscalizador', 'guard_name' => 'web']);
        $fiscalizador = User::factory()->create();
        $fiscalizador->assignRole($role);

        $this->actingAs($fiscalizador)
            ->get(route('users.index'))
            ->assertOk();
    }

    public function test_user_created_from_management_receives_verification_notification(): void
    {
        Notification::fake();
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole($role);

        $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Nuevo',
            'apellido' => 'Usuario',
            'email' => 'nuevo@example.com',
            'telefono' => '70000000',
            'ci' => '12345678',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('users.index'));

        $newUser = User::where('email', 'nuevo@example.com')->firstOrFail();

        Notification::assertSentTo($newUser, VerifyEmail::class);
        $this->assertNull($newUser->email_verified_at);
    }
}
