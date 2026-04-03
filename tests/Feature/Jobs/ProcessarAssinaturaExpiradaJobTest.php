<?php

namespace Tests\Feature\Jobs;

use App\Enums\StatusAssinatura;
use App\Jobs\ProcessarAssinaturasExpiradasJob;
use App\Models\Assinatura;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcessarAssinaturaExpiradaJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_deve_expirar_assinaturas_vencidas_e_desativar_usuarios()
    {
        $user = User::factory()->create(['is_active' => true]);

        $assinatura = Assinatura::factory()->create([
            'user_id' => $user->id,
            'status' => StatusAssinatura::ATIVA,
            'data_fim' => now()->subDay(),
        ]);

        (new ProcessarAssinaturasExpiradasJob())->handle();

        $this->assertEquals(
            StatusAssinatura::EXPIRADA,
            $assinatura->fresh()->status
        );

        $this->assertFalse((bool) $user->fresh()->is_active);
    }

    public function test_deve_processar_multiplas_assinaturas_vencidas()
    {
        $users = User::factory()->count(5)->create(['is_active' => true]);

        foreach ($users as $user) {
            Assinatura::factory()->create([
                'user_id' => $user->id,
                'status' => StatusAssinatura::ATIVA,
                'data_fim' => now()->subDays(10),
            ]);
        }

        (new ProcessarAssinaturasExpiradasJob())->handle();

        $this->assertEquals(
            0,
            Assinatura::where('status', StatusAssinatura::ATIVA)
                ->where('data_fim', '<', now())
                ->count()
        );

        $this->assertEquals(5, Assinatura::where('status', StatusAssinatura::EXPIRADA)->count());
        $this->assertEquals(0, User::where('is_active', true)->count());
    }

    public function test_nao_deve_expirar_assinatura_que_vence_exatamente_agora()
    {
        $agora = now();
        \Carbon\Carbon::setTestNow($agora);

        $user = User::factory()->create(['is_active' => true]);
        $assinatura = Assinatura::factory()->create([
            'user_id' => $user->id,
            'status' => StatusAssinatura::ATIVA,
            'data_fim' => $agora,
        ]);

        (new ProcessarAssinaturasExpiradasJob())->handle();

        $this->assertEquals(StatusAssinatura::ATIVA, $assinatura->fresh()->status);

        \Carbon\Carbon::setTestNow();
    }


    public function test_nao_deve_expirar_assinaturas_que_ainda_estao_no_prazo()
    {
        $user = User::factory()->create(['is_active' => true]);

        $assinatura = Assinatura::factory()->create([
            'user_id' => $user->id,
            'status' => StatusAssinatura::ATIVA,
            'data_fim' => now()->addDay(),
        ]);

        (new ProcessarAssinaturasExpiradasJob())->handle();

        $this->assertEquals(StatusAssinatura::ATIVA, $assinatura->fresh()->status);
        $this->assertTrue((bool) $user->fresh()->is_active);
    }

    public function test_nao_deve_processar_assinaturas_que_ja_estao_expiradas()
    {
        $user = User::factory()->create(['is_active' => false]);

        $assinatura = Assinatura::factory()->create([
            'user_id' => $user->id,
            'status' => StatusAssinatura::EXPIRADA,
            'data_fim' => now()->subMonth(),
        ]);

        (new ProcessarAssinaturasExpiradasJob())->handle();

        $this->assertDatabaseHas('assinaturas', [
            'id' => $assinatura->id,
            'status' => StatusAssinatura::EXPIRADA
        ]);
    }

    public function test_deve_fazer_rollback_se_ocorrer_erro_ao_atualizar_usuario()
    {
        $user = User::factory()->create(['is_active' => true]);
        $assinatura = Assinatura::factory()->create([
            'user_id' => $user->id,
            'status' => StatusAssinatura::ATIVA,
            'data_fim' => now()->subDay(),
        ]);

        /** * Usamos um Model Observer temporário (fake) para lançar uma exceção 
         * no exato momento em que o usuário tentar ser atualizado.
         */
        User::saving(function () {
            throw new \RuntimeException("Erro forçado na transação");
        });

        try {
            (new ProcessarAssinaturasExpiradasJob())->handle();
        } catch (\RuntimeException $e) {
            // Captura o erro
        }

        // Se a transação funcionou, o status da assinatura deve ter voltado para ATIVA
        $this->assertEquals(StatusAssinatura::ATIVA, $assinatura->fresh()->status);
        // O usuário também deve continuar ativo
        $this->assertTrue((bool) $user->fresh()->is_active);

        // Limpa o evento para não afetar outros testes
        User::flushEventListeners();
    }
}