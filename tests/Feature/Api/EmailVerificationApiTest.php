<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailVerificationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_unverified_mobile_user_can_check_email_status_but_not_use_business_api(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        $token = $user->createToken('mobile')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/email/estado')
            ->assertOk()
            ->assertJsonPath('verified', false);

        $this->withToken($token)
            ->getJson('/api/conductores')
            ->assertForbidden();
    }

    public function test_mobile_cannot_self_verify_without_the_signed_email_link(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        $token = $user->createToken('mobile')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/email/verificar')
            ->assertNotFound();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email_verified_at' => null,
        ]);
    }

    public function test_login_marks_the_account_as_verified_when_configured(): void
    {
        config(['linea61.auto_verify_on_login' => true]);

        $user = User::factory()->create([
            'email' => 'movil@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => null,
        ]);

        $this->postJson('/api/login', [
            'email' => 'movil@example.com',
            'password' => 'password',
            'device_name' => 'mobile',
        ])->assertOk()->assertJsonStructure(['access_token']);

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_profile_auto_verifies_an_existing_token_when_configured(): void
    {
        config(['linea61.auto_verify_on_login' => true]);

        $user = User::factory()->create(['email_verified_at' => null]);
        $token = $user->createToken('mobile')->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/me')
            ->assertOk();

        $this->assertNotNull($response->json('email_verified_at'));
        $this->assertNotNull($user->fresh()->email_verified_at);
    }
}
