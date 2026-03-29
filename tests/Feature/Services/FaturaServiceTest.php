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
        $this->gatewayMock = Mockery::mock(GatewayPagamentoInterface::class);
        $this->faturaService = new FaturaService($this->gatewayMock);
    }

    public function deve_criar_fatura_com_sucesso()
    {
        $user = User::factory()->create();
        
        $dadosFatura = [
            'valor' => 49.90,
            'status' => StatusPagamento::PENDENTE,
            'tipo_cobranca' => TipoCobranca::CICLO_NORMAL,
            'vencimento_em' => now()->addDays(3),
        ];

        $fatura = $this->faturaService->criarFatura($dadosFatura, $user->id);

        $this->assertInstanceOf(Fatura::class, $fatura);
        $this->assertEquals('49.90', $fatura->valor);
        $this->assertEquals(StatusPagamento::PENDENTE, $fatura->status);
        
        $this->assertDatabaseHas('faturas', [
            'user_id' => $user->id,
            'valor' => 49.90,
            'status' => StatusPagamento::PENDENTE->value // No banco checamos o value do Enum
        ]);
    }
    public function deve_gerar_link_de_pagamento_e_atualizar_referencia_externa()
    {
        $user = User::factory()->create();
        $plano = Plano::factory()->create(['plano' => Planos::BASICO, 'preco' => 20.00]);
        $assinatura = Assinatura::factory()->create(['user_id' => $user->id]);

        $this->gatewayMock->shouldReceive('criarPagamento')
            ->once()
            ->andReturn([
                'sandbox_init_point' => 'https://link-sandbox.com',
                'id' => 'REF-12345'
            ]);

        $resultado = $this->faturaService->processarFluxoFinanceiro($user, $assinatura, $plano);

        $this->assertEquals('https://link-sandbox.com', $resultado['redirect']);
        
        // Verifica se os campos fillable foram atualizados via service
        $this->assertDatabaseHas('faturas', [
            'user_id' => $user->id,
            'url_pagamento' => 'https://link-sandbox.com',
            'referencia_externa' => 'REF-12345',
            'tipo_cobranca' => TipoCobranca::CICLO_NORMAL->value
        ]);
    }

    public function deve_processar_pagamento_aprovado_com_conversao_de_metodo_do_mercado_pago()
    {
        // Setup: Usuário inativo com assinatura pendente
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

        // Mock dos dados que o Mercado Pago enviaria
        $dadosGateway = [
            'payment_type_id' => 'credit_card', // Isso será mapeado pelo seu MetodoPagamento::deMercadoPago
            'payment_method_id' => 'master',
            'status' => 'approved',
            'external_reference' => $fatura->id
        ];

        $this->faturaService->processarPagamentoAprovado($fatura, $dadosGateway);

        // Refresh nos modelos para pegar as alterações da Transaction
        $fatura->refresh();
        $user->refresh();
        $assinatura->refresh();

        $this->assertEquals(StatusPagamento::APROVADO, $fatura->status);
        $this->assertNotNull($fatura->pago_em);
        $this->assertTrue($user->is_active);
        $this->assertEquals(StatusAssinatura::ATIVA, $assinatura->status);
        
        // Verifica se a data de próxima cobrança foi estendida (addMonth)
        $this->assertTrue($assinatura->data_proxima_cobranca->isFuture());
    }

    public function deve_retornar_label_correta_do_metodo_de_pagamento()
    {
        // Este teste valida o accessor getMetodoPagamentoLabelAttribute da sua Model
        $faturaPendente = new Fatura(['metodo_pagamento' => null]);
        $this->assertEquals('Aguardando Pagamento', $faturaPendente->metodo_pagamento_label);

        // Simulando uma fatura com Cartão via Enum
        $faturaPaga = new Fatura(['metodo_pagamento' => MetodoPagamento::CARTAO_CREDITO]);
        // Aqui assume-se que seu Enum MetodoPagamento tem o método label()
        $this->assertNotEmpty($faturaPaga->metodo_pagamento_label);
    }
}