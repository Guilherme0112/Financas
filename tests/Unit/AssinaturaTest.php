<?php

namespace Tests\Unit;

use App\Models\Assinatura;
use Tests\TestCase;

class AssinaturaTest extends TestCase
{
    public function test_deve_calcular_proximo_vencimento_a_partir_do_vencimento_futuro(): void
    {
        $vencimentoFuturo = now()->addDays(7);
        $assinatura = new Assinatura(['data_proxima_cobranca' => $vencimentoFuturo]);

        $resultado = $assinatura->calcularProximoVencimento();

        $this->assertEquals(
            $vencimentoFuturo->copy()->addMonth()->toDateString(),
            $resultado->toDateString()
        );
    }

    public function test_deve_calcular_proximo_vencimento_a_partir_de_hoje_quando_ja_venceu(): void
    {
        $assinatura = new Assinatura(['data_proxima_cobranca' => now()->subDays(5)]);

        $resultado = $assinatura->calcularProximoVencimento();

        $this->assertEquals(
            now()->addMonth()->toDateString(),
            $resultado->toDateString()
        );
    }

    public function test_deve_calcular_proximo_vencimento_a_partir_de_hoje_quando_data_e_nula(): void
    {
        $assinatura = new Assinatura(['data_proxima_cobranca' => null]);

        $resultado = $assinatura->calcularProximoVencimento();

        $this->assertEquals(
            now()->addMonth()->toDateString(),
            $resultado->toDateString()
        );
    }

}
