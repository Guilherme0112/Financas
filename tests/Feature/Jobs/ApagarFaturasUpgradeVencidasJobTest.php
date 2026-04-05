<?php

namespace Tests\Feature\Jobs;

use App\Enums\StatusPagamento;
use App\Enums\StatusSolicitacaoMudancaPlano;
use App\Enums\TipoCobranca;
use App\Jobs\ApagarFaturasUpgradeVencidasJob;
use App\Models\Fatura;
use App\Models\SolicitacaoMudancaPlano;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApagarFaturasUpgradeVencidasJobTest extends TestCase
{
    use RefreshDatabase;

    private function criarFaturaUpgradePendente(array $overrides = []): Fatura
    {
        $user = User::factory()->create();

        return Fatura::factory()->create(array_merge([
            'tipo_cobranca' => TipoCobranca::UPGRADE,
            'status' => StatusPagamento::PENDENTE,
            'vencimento_em' => now()->subDay(),
            'user_id' => $user->id,
        ], $overrides));
    }

    private function dispatchJob(): void
    {
        (new ApagarFaturasUpgradeVencidasJob)->handle();
    }

    public function test_deve_deletar_fatura_de_upgrade_pendente_vencida(): void
    {
        $fatura = $this->criarFaturaUpgradePendente();
        $this->dispatchJob();
        $this->assertDatabaseMissing('faturas', ['id' => $fatura->id]);
    }

    public function test_deve_cancelar_solicitacao_pendente_ao_deletar_fatura(): void
    {
        $fatura = $this->criarFaturaUpgradePendente();

        SolicitacaoMudancaPlano::factory()->create([
            'fatura_id' => $fatura->id,
            'status' => StatusSolicitacaoMudancaPlano::PENDENTE,
        ]);

        $this->dispatchJob();

        $this->assertDatabaseMissing('faturas', ['id' => $fatura->id]);
        $this->assertDatabaseCount('solicitacoes_mudanca_plano', 0);
    }

    public function test_deve_processar_multiplas_faturas_vencidas_de_uma_vez(): void
    {
        $faturas = collect([
            $this->criarFaturaUpgradePendente(),
            $this->criarFaturaUpgradePendente(),
            $this->criarFaturaUpgradePendente(),
        ]);

        $this->dispatchJob();
        $this->assertDatabaseCount('faturas', 0);
    }

    public function test_nao_deve_deletar_fatura_de_upgrade_ja_paga(): void
    {
        $fatura = $this->criarFaturaUpgradePendente([
            'status' => StatusPagamento::APROVADO,
        ]);

        $this->dispatchJob();

        $this->assertDatabaseHas('faturas', ['id' => $fatura->id]);
    }

    public function test_nao_deve_deletar_fatura_de_ciclo_normal_pendente_vencida(): void
    {
        $fatura = Fatura::factory()->create([
            'tipo_cobranca' => TipoCobranca::CICLO_NORMAL,
            'status' => StatusPagamento::PENDENTE,
            'vencimento_em' => now()->subDay(),
        ]);

        $this->dispatchJob();

        $this->assertDatabaseHas('faturas', ['id' => $fatura->id]);
    }

    public function test_nao_deve_deletar_fatura_de_upgrade_pendente_ainda_nao_vencida(): void
    {
        $fatura = $this->criarFaturaUpgradePendente([
            'vencimento_em' => now()->addDay(),
        ]);

        $this->dispatchJob();

        $this->assertDatabaseHas('faturas', ['id' => $fatura->id]);
    }

    public function test_nao_deve_alterar_solicitacao_ja_aprovada(): void
    {
        $fatura = $this->criarFaturaUpgradePendente();

        $solicitacao = SolicitacaoMudancaPlano::factory()->create([
            'fatura_id' => $fatura->id,
            'status' => StatusSolicitacaoMudancaPlano::CONCLUIDO,
        ]);

        $this->dispatchJob();

        $this->assertDatabaseMissing('faturas', ['id' => $fatura->id]);
        $this->assertDatabaseMissing('solicitacoes_mudanca_plano', ['id' => $solicitacao->id]);
    }

    public function test_nao_deve_alterar_solicitacao_ja_concluida(): void
    {
        $fatura = $this->criarFaturaUpgradePendente();

        $solicitacao = SolicitacaoMudancaPlano::factory()->create([
            'fatura_id' => $fatura->id,
            'status' => StatusSolicitacaoMudancaPlano::CONCLUIDO,
        ]);

        $this->dispatchJob();

        $this->assertDatabaseMissing('faturas', ['id' => $fatura->id]);
        $this->assertDatabaseMissing('solicitacoes_mudanca_plano', ['id' => $solicitacao->id]);
    }

    public function test_nao_deve_alterar_solicitacao_ja_cancelada(): void
    {
        $fatura = $this->criarFaturaUpgradePendente();

        $solicitacao = SolicitacaoMudancaPlano::factory()->create([
            'fatura_id' => $fatura->id,
            'status' => StatusSolicitacaoMudancaPlano::CANCELADO,
        ]);

        $this->dispatchJob();

        $this->assertDatabaseMissing('faturas', ['id' => $fatura->id]);
        $this->assertDatabaseMissing('solicitacoes_mudanca_plano', ['id' => $solicitacao->id]);
    }

    public function test_nao_deve_fazer_nada_se_nao_houver_faturas_vencidas(): void
    {
        $this->dispatchJob();

        $this->assertDatabaseCount('faturas', 0);
        $this->assertDatabaseCount('solicitacoes_mudanca_plano', 0);
    }

    public function test_deve_deletar_apenas_as_vencidas_mantendo_as_validas(): void
    {
        $faturaVencida = $this->criarFaturaUpgradePendente(['vencimento_em' => now()->subDays(3)]);
        $faturaValida = $this->criarFaturaUpgradePendente(['vencimento_em' => now()->addDays(3)]);

        $this->dispatchJob();

        $this->assertDatabaseMissing('faturas', ['id' => $faturaVencida->id]);
        $this->assertDatabaseHas('faturas', ['id' => $faturaValida->id]);
    }
}
