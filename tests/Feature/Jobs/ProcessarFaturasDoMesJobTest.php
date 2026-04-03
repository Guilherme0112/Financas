<?php

namespace Tests\Feature\Jobs;

use App\Contracts\GatewayPagamentoInterface;
use App\Enums\StatusAssinatura;
use App\Jobs\ProcessarFaturasDoMesJob;
use App\Models\Assinatura;
use App\Models\Fatura;
use App\Models\Plano;
use App\Models\User;
use App\Services\AssinaturaService;
use App\Services\FaturaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery\MockInterface;

class ProcessarFaturasDoMesJobTest extends TestCase
{
    use RefreshDatabase;

    private MockInterface $mockGateway;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockGateway = $this->mock(GatewayPagamentoInterface::class);
    }

    public function test_deve_gerar_faturas_para_assinaturas_perto_do_vencimento()
    {
        $user = User::factory()->create();
        $plano = Plano::factory()->create(['preco' => 150.00]);

        $assinatura = Assinatura::factory()->create([
            'user_id' => $user->id,
            'plano_id' => $plano->id,
            'data_proxima_cobranca' => now()->addDays(7)->startOfDay(),
        ]);

        $this->mockGateway->shouldReceive('criarPagamento')
            ->once()
            ->andReturn([
                'sandbox_init_point' => 'https://payment.link/test',
                'id' => 'REF_EXT_123'
            ]);

        $job = new ProcessarFaturasDoMesJob();
        $job->handle(
            app(AssinaturaService::class),
            $this->mockGateway,
            app(FaturaService::class)
        );

        $this->assertDatabaseHas('faturas', [
            'user_id' => $user->id,
            'referencia_externa' => 'REF_EXT_123'
        ]);

        $this->assertEquals(
            now()->addDays(7)->addMonth()->toDateString(),
            $assinatura->fresh()->data_proxima_cobranca->toDateString()
        );
    }


    public function test_deve_continuar_processando_outras_faturas_se_uma_falhar()
    {
        $assinaturas = Assinatura::factory()->count(2)->create([
            'data_proxima_cobranca' => now()->addDays(7),
        ]);

        $this->mockGateway->shouldReceive('criarPagamento')
            ->twice()
            ->andReturn(
                fn() => throw new \Exception("Erro na primeira"),
                ['id' => 'SUCESSO_2', 'sandbox_init_point' => 'link']
            );

        $job = new ProcessarFaturasDoMesJob();
        $job->handle(app(AssinaturaService::class), $this->mockGateway, app(FaturaService::class));

        $this->assertDatabaseCount('faturas', 1);
    }

    public function test_fatura_deve_ter_o_valor_exato_do_plano_no_momento_da_geracao()
    {
        $plano = Plano::factory()->create(['preco' => 20.00]);
        $assinatura = Assinatura::factory()->create([
            'plano_id' => $plano->id,
            'data_proxima_cobranca' => now()->addDays(7),
        ]);

        $this->mockGateway->shouldReceive('criarPagamento')->andReturn(['id' => '1', 'sandbox_init_point' => 'link']);

        $job = new ProcessarFaturasDoMesJob();
        $job->handle(app(AssinaturaService::class), $this->mockGateway, app(FaturaService::class));

        $this->assertDatabaseHas('faturas', [
            'assinatura_id' => $assinatura->id,
            'valor' => 20.00
        ]);
    }

    public function test_nao_deve_gerar_fatura_para_assinaturas_canceladas()
    {
        Assinatura::factory()->create([
            'status' => StatusAssinatura::CANCELADA,
            'data_proxima_cobranca' => now()->addDays(7),
        ]);

        $this->mockGateway->shouldNotReceive('criarPagamento');

        $job = new ProcessarFaturasDoMesJob();
        $job->handle(app(AssinaturaService::class), $this->mockGateway, app(FaturaService::class));

        $this->assertDatabaseCount('faturas', 0);
    }

    public function test_deve_fazer_rollback_se_gateway_falhar()
    {
        $user = User::factory()->create();
        $plano = Plano::factory()->create(['preco' => 150.00]);

        $assinatura = Assinatura::factory()->create([
            'user_id' => $user->id,
            'plano_id' => $plano->id,
            'data_proxima_cobranca' => now()->addDays(7)->startOfDay(),
        ]);

        $this->mockGateway->shouldReceive('criarPagamento')
            ->andThrow(new \Exception("Erro na API"));

        try {
            $job = new ProcessarFaturasDoMesJob();
            $job->handle(app(AssinaturaService::class), $this->mockGateway, app(FaturaService::class));
        } catch (\Exception $e) {
            // Silencia a exception para continuar o assert
        }

        $this->assertDatabaseCount('faturas', 0);
        $this->assertEquals(
            now()->addDays(7)->toDateString(),
            $assinatura->fresh()->data_proxima_cobranca->toDateString()
        );
    }

    public function test_nao_deve_processar_se_nao_houver_assinaturas_vencendo()
    {
        Assinatura::factory()->create([
            'data_proxima_cobranca' => now()->addDays(15)->startOfDay()
        ]);

        $this->mockGateway->shouldNotReceive('criarPagamento');

        $job = new ProcessarFaturasDoMesJob();
        $job->handle(app(AssinaturaService::class), $this->mockGateway, app(FaturaService::class));

        $this->assertDatabaseCount('faturas', 0);
    }
    public function test_nao_deve_gerar_fatura_duplicada_se_ja_existir_uma_para_o_periodo()
    {
        $user = User::factory()->create();
        $plano = Plano::factory()->create(['preco' => 150.00]);
        $assinatura = Assinatura::factory()->create([
            'user_id' => $user->id,
            'plano_id' => $plano->id,
            'data_proxima_cobranca' => now()->addDays(7)->startOfDay(),
        ]);

        Fatura::factory()->create([
            'assinatura_id' => $assinatura->id,
            'vencimento_em' => $assinatura->data_proxima_cobranca,
        ]);

        $this->mockGateway->shouldNotReceive('criarPagamento');

        $job = new ProcessarFaturasDoMesJob();
        $job->handle(app(AssinaturaService::class), $this->mockGateway, app(FaturaService::class));

        $this->assertDatabaseCount('faturas', 1);
    }
    public function test_deve_atualizar_data_proxima_cobranca_corretamente_no_fim_do_mes()
    {
        $this->travelTo(now()->setYear(2024)->setMonth(1)->setDay(24));

        $assinatura = Assinatura::factory()->create([
            'data_proxima_cobranca' => now()->addDays(7)->startOfDay(),
        ]);

        $this->mockGateway->shouldReceive('criarPagamento')->andReturn(['id' => '1', 'sandbox_init_point' => 'link']);

        $job = new ProcessarFaturasDoMesJob();
        $job->handle(app(AssinaturaService::class), $this->mockGateway, app(FaturaService::class));

        $this->assertEquals('2024-02-29', $assinatura->fresh()->data_proxima_cobranca->toDateString());
    }
}