<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleAssignmentAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_admin_can_manage_roles(): void
    {
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin)
            ->get(route('roles.assign'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('roles.index'))
            ->assertOk();
    }

    public function test_fiscalizador_can_manage_roles(): void
    {
        $fiscalizador = $this->createUserWithRole('fiscalizador');

        $this->actingAs($fiscalizador)
            ->get(route('roles.assign'))
            ->assertOk();

        $this->actingAs($fiscalizador)
            ->get(route('roles.index'))
            ->assertOk();
    }

    public function test_propietario_cannot_manage_roles(): void
    {
        $propietario = $this->createUserWithRole('propietario');

        $this->actingAs($propietario)
            ->get(route('roles.assign'))
            ->assertForbidden();

        $this->actingAs($propietario)
            ->get(route('roles.index'))
            ->assertForbidden();
    }

    public function test_roles_menu_is_only_visible_to_authorized_roles(): void
    {
        $admin = $this->createUserWithRole('admin');
        $fiscalizador = $this->createUserWithRole('fiscalizador');
        $propietario = $this->createUserWithRole('propietario');

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('roles.assign'));

        $this->actingAs($fiscalizador)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('roles.assign'));

        $this->actingAs($propietario)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee(route('roles.assign'));
    }

    private function createUserWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
