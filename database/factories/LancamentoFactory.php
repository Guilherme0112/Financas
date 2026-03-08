<?php

namespace Database\Factories;

use App\Models\Meta;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class LancamentoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => $this->faker->sentence(3),
            'tipo' => 'ENTRADA',
            'categoria_entrada' => 'SALARIO',
            'valor' => $this->faker->randomFloat(2, 10, 1000),
            'mes_referencia' => now()->format('Y-m-d'),
            'user_id' => User::factory(),
            'meta_id' => Meta::factory(),
        ];
    }
}
