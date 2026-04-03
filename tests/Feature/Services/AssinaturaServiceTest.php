<?php

namespace Tests\Feature\Services;

use App\Contracts\GatewayPagamentoInterface;
use App\Enums\Planos;
use App\Enums\StatusAssinatura;
use App\Enums\StatusPagamento;
use App\Enums\StatusSolicitacaoMudancaPlano;
use App\Enums\TipoCobranca;
use App\Models\Assinatura;
use App\Models\Fatura;
use App\Models\Plano;
use App\Models\User;
use App\Services\AssinaturaService;
use App\Services\SolicitacaoMudancaPlanoService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssinaturaServiceTest extends TestCase
{
    use RefreshDatabase;
    private AssinaturaService $assinaturaService;
    private Assinatura $assinatura;
    private User $user;

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

        $this->assinaturaService = app(AssinaturaService::class);
        $this->user = User::factory()->create();
        $this->assinatura = $this->user->assinatura;
    }

    public function test_nao_deve_preparar_upgrade_se_assinatura_nao_pertencer_ao_usuario(): void
    {
        $outroUsuario = User::factory()->create();
        $novoPlano = Plano::factory()->create();
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->assinaturaService->prepararUpgrade(
            ['plano_id' => $novoPlano->id],
            $outroUsuario->assinatura,
            $this->user->id
        );
    }

    public function test_deve_preparar_upgrade_de_plano(): void
    {
        $novoPlano = Plano::factory()->create(['plano' => Planos::BASICO, 'preco' => 20.00]);
        $url = $this->assinaturaService->prepararUpgrade(
            ['plano_id' => $novoPlano->id],
            $this->assinatura,
            $this->user->id
        );

        $this->assertEquals('https://sandbox.mercadopago.com/test', $url);

        $this->assertDatabaseHas('faturas', [
            'assinatura_id' => $this->assinatura->id,
            'valor' => 20.00,
            'user_id' => $this->user->id,
            'tipo_cobranca' => TipoCobranca::UPGRADE
        ]);

        $this->assertDatabaseHas('solicitacoes_mudanca_plano', [
            'assinatura_id' => $this->assinatura->id,
            'plano_novo_id' => $novoPlano->id,
            'user_id' => $this->user->id,
            'status' => StatusSolicitacaoMudancaPlano::PENDENTE
        ]);
    }

    public function test_excluir_deve_falhar_se_assinatura_nao_existir(): void
    {
        $idInexistente = 9999;
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->assinaturaService->excluir($idInexistente, $this->user->id);
    }

    public function test_nao_deve_criar_nova_solicitacao_se_ja_houver_uma_pendente(): void
    {
        $planoA = Plano::factory()->create();
        $planoB = Plano::factory()->create();

        $this->assinaturaService->fazerUpgrade($this->assinatura, $planoA);

        $this->expectException(\Exception::class);
        $this->assinaturaService->fazerUpgrade($this->assinatura, $planoB);
    }

    public function test_deve_confirmar_upgrade_e_atualizar_plano_da_assinatura(): void
    {
        $novoPlano = Plano::factory()->create(['plano' => Planos::BASICO, 'preco' => 20.00]);
        $fatura = Fatura::factory()->create([
            'user_id' => $this->user->id,
            'assinatura_id' => $this->assinatura->id,
            'tipo_cobranca' => TipoCobranca::UPGRADE,
            'status' => StatusPagamento::PENDENTE
        ]);
        $mudancaService = app(SolicitacaoMudancaPlanoService::class);
        $mudancaService->criarSolicitacaoMudancaPlano([
            'assinatura_id' => $this->assinatura->id,
            'fatura_id' => $fatura->id,
            'plano_antigo_id' => $this->assinatura->plano_id,
            'plano_novo_id' => $novoPlano->id,
            'status' => StatusSolicitacaoMudancaPlano::PENDENTE,
        ], $this->user->id);

        $this->assinaturaService->confirmarUpgrade($fatura);
        $this->assinatura->refresh();
        $this->assertEquals($novoPlano->id, $this->assinatura->plano_id);
        $this->assertEquals(StatusAssinatura::ATIVA, $this->assinatura->status);

        $this->assertDatabaseHas('solicitacoes_mudanca_plano', [
            'fatura_id' => $fatura->id,
            'user_id' => $this->user->id,
            'status' => StatusSolicitacaoMudancaPlano::CONCLUIDO
        ]);
    }

    public function test_deve_preparar_assinatura_inicial_gratuita_com_datas_corretas(): void
    {
        $hoje = Carbon::create(2026, 04, 01, 10, 00, 00);
        Carbon::setTestNow($hoje);
        $planoGratuito = Plano::factory()->create(['plano' => Planos::GRATUITO]);
        $assinatura = $this->assinaturaService->prepararAssinaturaInicial($this->user, $planoGratuito);
        $this->assertEquals(StatusAssinatura::ATIVA, $assinatura->status);
        $this->assertEquals($planoGratuito->id, $assinatura->plano_id);
        $this->assertEquals('2026-04-08', $assinatura->data_fim->toDateString());
        Carbon::setTestNow();
    }

    public function test_nao_deve_confirmar_upgrade_se_fatura_ja_estiver_paga(): void
    {
        $fatura = Fatura::factory()->create([
            'user_id' => $this->user->id,
            'assinatura_id' => $this->assinatura->id,
            'tipo_cobranca' => TipoCobranca::UPGRADE,
            'status' => StatusPagamento::APROVADO,
            'pago_em' => now()->subDay()
        ]);
        $dataPagamentoOriginal = $fatura->pago_em->toDateTimeString();
        $this->assinaturaService->confirmarUpgrade($fatura);
        $this->assertEquals($dataPagamentoOriginal, $fatura->refresh()->pago_em->toDateTimeString());
    }

    public function test_nao_deve_atualizar_assinatura_se_solicitacao_nao_for_encontrada(): void
    {
        $fatura = Fatura::factory()->create([
            'user_id' => $this->user->id,
            'assinatura_id' => $this->assinatura->id,
            'tipo_cobranca' => TipoCobranca::UPGRADE,
            'status' => StatusPagamento::PENDENTE
        ]);
        $planoAntigoId = $this->assinatura->plano_id;
        $this->assinaturaService->confirmarUpgrade($fatura);
        $this->assinatura->refresh();
        $this->assertEquals($planoAntigoId, $this->assinatura->plano_id);
    }
}
