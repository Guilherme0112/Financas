<?php

namespace Database\Factories;

use App\Enums\Planos;
use App\Models\Assinatura;
use App\Models\Plano;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => "Test User",
            'email' => "test@example.com",
            'email_verified_at' => now(),
            'phone' => "11111111111",
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (User $user) {
            if (!$user->assinatura_id) {
                // Verifica se o plano já existe antes de deixar a AssinaturaFactory criar um novo
                $plano = Plano::where('plano', Planos::BASICO)->first()
                    ?? Plano::factory()->create(['plano' => Planos::BASICO]);

                $assinatura = Assinatura::factory()->create([
                    'user_id' => $user->id,
                    'plano_id' => $plano->id,
                ]);

                $user->update(['assinatura_id' => $assinatura->id]);
            }
        });
    }
    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
