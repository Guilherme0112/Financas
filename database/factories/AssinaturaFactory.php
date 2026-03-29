<?php

namespace Database\Factories;

use App\Enums\StatusAssinatura;
use App\Models\Plano;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assinatura>
 */
class AssinaturaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
public function definition(): array
{
    $dataInicio = now()->subDays(rand(1, 30));

    return [
        'user_id' => User::factory(),
        'plano_id' => fn() => Plano::first() ?? Plano::factory(),
        'data_inicio' => $dataInicio,
        'data_fim' => (clone $dataInicio)->addMonth(),
        'data_proxima_cobranca' => (clone $dataInicio)->addMonth(),
        'status' => StatusAssinatura::ATIVA,
    ];
}

    /**
     * Estado para assinaturas pendentes (aguardando pagamento)
     */
    public function pendente(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusAssinatura::PENDENTE,
            'data_inicio' => null,
            'data_fim' => null,
        ]);
    }

    /**
     * Estado para assinaturas expiradas
     */
    public function expirada(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusAssinatura::EXPIRADA,
            'data_fim' => now()->subDay(),
        ]);
    }
}
