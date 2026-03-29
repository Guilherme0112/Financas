<?php

namespace Database\Factories;

use App\Enums\Planos;
use App\Models\Plano;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plano>
 */
class PlanoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => 'Plano Básico',
            'plano' => Planos::BASICO,
            'descricao' => 'Acesso limitado a recursos básicos.',
            'preco' => 20.00,
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Plano $plano) {
            $existing = Plano::where('plano', $plano->plano)->first();
            if ($existing) {
                // Retorna o existente sem inserir
                $plano->id = $existing->id;
                $plano->exists = true;
            }
        });
    }
}
