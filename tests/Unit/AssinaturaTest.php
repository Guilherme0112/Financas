<?php

namespace Tests\Unit;

use App\Enums\Planos;
use App\Enums\StatusAssinatura;
use App\Models\Assinatura;
use App\Models\Plano;
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

    public function deve_configurar_corretamente_uma_assinatura_para_plano_gratuito()
    {
        $plano = Plano::factory()->create(['plano' => Planos::GRATUITO]);

        $assinatura = Assinatura::configurarDatasIniciais($plano);

        $this->assertInstanceOf(Assinatura::class, $assinatura);
        $this->assertEquals($plano->id, $assinatura->plano_id);
        $this->assertEquals(StatusAssinatura::ATIVA, $assinatura->status);
        
        $this->assertNotNull($assinatura->data_inicio);
        $this->assertEquals(now()->addDays(7)->toDateString(), $assinatura->data_fim->toDateString());
        $this->assertEquals($assinatura->data_fim->toDateString(), $assinatura->data_proxima_cobranca->toDateString());
    }

    public function deve_configurar_corretamente_uma_assinatura_para_plano_pago()
    {
        $plano = Plano::factory()->create(['plano' => Planos::BASICO]);

        $assinatura = Assinatura::configurarDatasIniciais($plano);

        $this->assertEquals(StatusAssinatura::PENDENTE, $assinatura->status);
        $this->assertNull($assinatura->data_inicio);
        $this->assertNull($assinatura->data_fim);
        $this->assertNull($assinatura->data_proxima_cobranca);
    }
}
