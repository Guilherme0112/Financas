<?php

namespace Tests\Feature\Services;

use App\Contracts\GatewayPagamentoInterface;
use App\Enums\MetodoPagamento;
use App\Enums\Planos;
use App\Enums\StatusPagamento;
use App\Enums\StatusAssinatura;
use App\Enums\TipoCobranca;
use App\Models\Assinatura;
use App\Models\Fatura;
use App\Models\Plano;
use App\Models\User;
use App\Services\FaturaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class FaturaServiceTest extends TestCase
{
    use RefreshDatabase;

    private FaturaService $faturaService;
    private $gatewayMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mock(GatewayPagamentoInterface::class, function ($mock) {
            $mock->shouldReceive('criarPagamento')
                ->andReturn([
                    'id' => 'pref_test_123',
                    'sandbox_init_point' => 'https://sandbox.mercadopago.com/test'
                ]);
        });
        $this->gatewayMock = Mockery::mock(GatewayPagamentoInterface::class);
        $this->faturaService = new FaturaService($this->gatewayMock);
    }

    public function test_deve_criar_fatura_com_sucesso()
    {
        $user = User::factory()->create();
        
        $dadosFatura = [
            'valor' => 20.00,
            'status' => StatusPagamento::PENDENTE,
            'tipo_cobranca' => TipoCobranca::CICLO_NORMAL,
            'vencimento_em' => now()->addDays(3),
        ];

        $fatura = $this->faturaService->criarFatura($dadosFatura, $user->id);

        $this->assertInstanceOf(Fatura::class, $fatura);
        $this->assertEquals('20.00', $fatura->valor);
        $this->assertEquals(StatusPagamento::PENDENTE, $fatura->status);
        
        $this->assertDatabaseHas('faturas', [
            'user_id' => $user->id,
            'valor' => 20.00,
            'status' => StatusPagamento::PENDENTE->value
        ]);
    }
    public function test_deve_gerar_link_de_pagamento_e_atualizar_referencia_externa()
    {
        $user = User::factory()->create();
        $plano = Plano::factory()->create(['plano' => Planos::BASICO, 'preco' => 20.00]);
        $assinatura = Assinatura::factory()->create(['user_id' => $user->id]);

        $resultado = $this->faturaService->processarFluxoFinanceiro($user, $assinatura, $plano);
        $this->assertEquals('https://link-sandbox.com', $resultado['redirect']);
        
        $this->assertDatabaseHas('faturas', [
            'user_id' => $user->id,
            'url_pagamento' => 'https://link-sandbox.com',
            'referencia_externa' => 'REF-12345',
            'tipo_cobranca' => TipoCobranca::CICLO_NORMAL->value
        ]);
    }

    public function test_nao_deve_estender_assinatura_duas_vezes_se_receber_pagamento_duplicado(): void
    {
        $assinatura = Assinatura::factory()->create([
            'status' => StatusAssinatura::ATIVA,
            'data_proxima_cobranca' => now()->addDays(7)
        ]);
        $fatura = Fatura::factory()->create([
            'assinatura_id' => $assinatura->id,
            'status' => StatusPagamento::PENDENTE
        ]);
        $dados = ['status' => 'approved', 'payment_method_id' => 'visa', "payment_type_id" => ""];

        $this->faturaService->processarPagamentoAprovado($fatura, $dados);
        $dataPrimeiraExtensao = $assinatura->refresh()->data_proxima_cobranca;

        $this->faturaService->processarPagamentoAprovado($fatura, $dados);
        $this->assertEquals($dataPrimeiraExtensao->toDateTimeString(), $assinatura->refresh()->data_proxima_cobranca->toDateTimeString());
    }

    public function test_deve_processar_pagamento_aprovado_com_conversao_de_metodo_do_mercado_pago()
    {
        $user = User::factory()->create(['is_active' => false]);
        $assinatura = Assinatura::factory()->create([
            'user_id' => $user->id,
            'status' => StatusAssinatura::PENDENTE,
        ]);
        
        $fatura = Fatura::factory()->create([
            'user_id' => $user->id,
            'assinatura_id' => $assinatura->id,
            'status' => StatusPagamento::PENDENTE,
            'tipo_cobranca' => TipoCobranca::CICLO_NORMAL
        ]);

        $dadosGateway = [
            'payment_type_id' => 'credit_card',
            'payment_method_id' => 'master',
            'status' => 'approved',
            'external_reference' => $fatura->id
        ];

        $this->faturaService->processarPagamentoAprovado($fatura, $dadosGateway);

        $fatura->refresh();
        $user->refresh();
        $assinatura->refresh();

        $this->assertEquals(StatusPagamento::APROVADO, $fatura->status);
        $this->assertNotNull($fatura->pago_em);
        $this->assertTrue($user->is_active);
        $this->assertEquals(StatusAssinatura::ATIVA, $assinatura->status);
        
        $this->assertTrue($assinatura->data_proxima_cobranca->isFuture());
    }

    public function test_deve_retornar_label_correta_do_metodo_de_pagamento()
    {
        $faturaPendente = new Fatura(['metodo_pagamento' => null]);
        $this->assertEquals('Aguardando Pagamento', $faturaPendente->metodo_pagamento_label);

        $faturaPaga = new Fatura(['metodo_pagamento' => MetodoPagamento::CARTAO_CREDITO]);

        $this->assertNotEmpty($faturaPaga->metodo_pagamento_label);
    }
}