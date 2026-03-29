<?php

namespace Tests\Feature\Auth;

use App\Enums\Planos;
use App\Models\Plano;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $plano = Plano::factory()->create([
            'nome' => 'Gratuito',
            'plano' => Planos::GRATUITO
        ]);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '11111111111',
            'password' => 'password',
            'password_confirmation' => 'password',
            'plano' => $plano->plano->value 
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
