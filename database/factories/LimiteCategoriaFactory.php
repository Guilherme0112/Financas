<?php

namespace Database\Factories;

use App\Enums\CategoriaSaida;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LimiteCategoria>
 */
class LimiteCategoriaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'categoria_saida' => CategoriaSaida::ALIMENTACAO->value,
            'limite' => 1000,
            'mes_referencia' => '2026-03-01',
            'notificar_ao_atingir' => true
        ];
    }
}
