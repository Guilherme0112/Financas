<?php

namespace Database\Factories;

use App\Enums\StatusSolicitacaoMudancaPlano;
use App\Models\Assinatura;
use App\Models\Fatura;
use App\Models\Plano;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SolicitacaoMudancaPlano>
 */
class SolicitacaoMudancaPlanoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'assinatura_id' => Assinatura::factory(),
            'fatura_id' => Fatura::factory(),
            'plano_antigo_id' => Plano::factory(),
            'plano_novo_id' => Plano::factory(),
            'status' => StatusSolicitacaoMudancaPlano::PENDENTE,
        ];
    }

    public function pendente(): static
    {
        return $this->state(['status' => StatusSolicitacaoMudancaPlano::PENDENTE]);
    }

    public function concluido(): static
    {
        return $this->state(['status' => StatusSolicitacaoMudancaPlano::CONCLUIDO]);
    }

    public function cancelado(): static
    {
        return $this->state(['status' => StatusSolicitacaoMudancaPlano::CANCELADO]);
    }
}
