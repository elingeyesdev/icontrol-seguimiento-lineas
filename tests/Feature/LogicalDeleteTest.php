<?php

namespace Tests\Feature;

use App\Models\Parada;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LogicalDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_parada_destroy_uses_logical_delete_and_excludes_it_from_api_results(): void
    {
        $user = User::factory()->create();
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $user->assignRole('admin');
        $this->actingAs($user, 'sanctum');

        $parada = Parada::create([
            'nombre' => 'Parada Test',
            'referencia' => 'Referencia Test',
            'latitud' => -17.783,
            'longitud' => -63.182,
            'estado' => 'activo',
        ]);

        $response = $this->delete('/api/paradas/'.$parada->id);

        $response->assertOk();
        $this->assertDatabaseHas('paradas', [
            'id' => $parada->id,
            'estado' => 'inactivo',
        ]);

        $listado = $this->get('/api/paradas');
        $listado->assertStatus(200)
            ->assertJsonMissing([
                'id' => $parada->id,
            ]);
    }
}
