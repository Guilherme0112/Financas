<?php

namespace Database\Factories;

use App\Enums\StatusPagamento;
use App\Enums\TipoCobranca;
use App\Enums\MetodoPagamento;
use App\Models\Assinatura;
use App\Models\Fatura;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FaturaFactory extends Factory
{
    protected $model = Fatura::class;

    public function definition(): array
    {
        $inicio = now()->startOfMonth();
        $fim = (clone $inicio)->endOfMonth();

        return [
            'user_id' => User::factory(),
            'assinatura_id' => Assinatura::factory(),
            'valor' => $this->faker->randomElement([19.90, 29.90, 49.90]),
            'status' => StatusPagamento::PENDENTE,
            'metodo_pagamento' => null,
            'vencimento_em' => now()->addDays(3),
            'periodo_inicio' => $inicio,
            'periodo_fim' => $fim,
            'referencia_externa' => 'pref_' . $this->faker->uuid,
            'url_pagamento' => 'https://www.mercadopago.com.br/checkout/v1/redirect?pref_id=' . $this->faker->uuid,
            'pago_em' => null,
            'tipo_cobranca' => TipoCobranca::CICLO_NORMAL,
        ];
    }

    /**
     * Estado para uma fatura já paga
     */
    public function paga(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusPagamento::APROVADO,
            'metodo_pagamento' => MetodoPagamento::PIX,
            'pago_em' => now(),
        ]);
    }

    /**
     * Estado para uma fatura de Upgrade
     */
    public function upgrade(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo_cobranca' => TipoCobranca::UPGRADE,
        ]);
    }
}