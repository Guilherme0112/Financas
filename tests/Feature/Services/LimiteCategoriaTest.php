<?php

namespace Tests\Feature\Services;

use App\Models\User;
use App\Models\Lancamento;
use App\Models\LimiteCategoria;
use App\Enums\CategoriaSaida;
use App\Repositories\LimiteCategoriaRepository;
use App\Services\LimiteCategoriaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class LimiteCategoriaTest extends TestCase
{
    use RefreshDatabase;

    private LimiteCategoriaService $service;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LimiteCategoriaService(new LimiteCategoriaRepository());
        $this->user = User::factory()->create();
    }

    public function test_deve_criar_um_limite_de_categoria()
    {
        $dados = [
            'categoria_saida' => CategoriaSaida::ALIMENTACAO->value,
            'limite' => 1000,
            'mes_referencia' => '2026-03-01',
            'notificar_ao_atingir' => true,
        ];

        $resultado = $this->service->criar($dados, $this->user->id);

        $this->assertInstanceOf(LimiteCategoria::class, $resultado);
        $this->assertDatabaseHas('limite_categorias', [
            'categoria_saida' => CategoriaSaida::ALIMENTACAO->value,
            'limite' => 1000,
            'user_id' => $this->user->id
        ]);
    }

    public function test_deve_atualizar_um_limite_de_categoria()
    {
        $limite = LimiteCategoria::factory()->create(['user_id' => $this->user->id]);

        $dadosAtualizados = [
            'limite' => 2000,
            'notificar_ao_atingir' => false,
        ];

        $resultado = $this->service->atualizar($limite->id, $dadosAtualizados, $this->user->id);

        $this->assertInstanceOf(LimiteCategoria::class, $resultado);
        $this->assertEquals(2000, $resultado->limite);
        $this->assertFalse($resultado->notificar_ao_atingir);
    }

    public function test_deve_falhar_ao_atualizar_limite_de_outro_usuario()
    {
        $outroUser = User::factory()->create();
        $limiteAlheio = LimiteCategoria::factory()->create(['user_id' => $outroUser->id]);

        $this->expectException(ModelNotFoundException::class);

        $this->service->atualizar($limiteAlheio->id, ['limite' => 500], $this->user->id);
    }

    public function test_deve_excluir_limite_com_sucesso()
    {
        $limite = LimiteCategoria::factory()->create(['user_id' => $this->user->id]);

        $this->service->excluir($limite->id, $this->user->id);

        $this->assertDatabaseMissing('limite_categorias', ['id' => $limite->id]);
    }

    public function test_deve_falhar_ao_excluir_limite_de_outro_usuario()
    {
        $outroUser = User::factory()->create();
        $limiteAlheio = LimiteCategoria::factory()->create(['user_id' => $outroUser->id]);

        $this->expectException(ModelNotFoundException::class);

        $this->service->excluir($limiteAlheio->id, $this->user->id);
    }

    public function test_deve_calcular_corretamente_a_soma_dos_lancamentos_da_categoria_no_mes()
    {
        $mes = '2026-03-01';
        $limite = LimiteCategoria::factory()->create([
            'user_id' => $this->user->id,
            'categoria_saida' => CategoriaSaida::ALIMENTACAO,
            'mes_referencia' => $mes
        ]);

        // Lançamento 1: Mesma categoria, mesmo mês (DEVE SOMAR)
        Lancamento::factory()->create([
            'user_id' => $this->user->id,
            'categoria_saida' => CategoriaSaida::ALIMENTACAO,
            'valor' => 150,
            'mes_referencia' => '2026-03-10'
        ]);

        // Lançamento 2: Mesma categoria, mesmo mês (DEVE SOMAR)
        Lancamento::factory()->create([
            'user_id' => $this->user->id,
            'categoria_saida' => CategoriaSaida::ALIMENTACAO,
            'valor' => 50,
            'mes_referencia' => '2026-03-15'
        ]);

        // Lançamento 3: Mesma categoria, MÊS DIFERENTE (NÃO DEVE SOMAR)
        Lancamento::factory()->create([
            'user_id' => $this->user->id,
            'categoria_saida' => CategoriaSaida::ALIMENTACAO,
            'valor' => 500,
            'mes_referencia' => '2026-04-01'
        ]);

        // Lançamento 4: CATEGORIA DIFERENTE, mesmo mês (NÃO DEVE SOMAR)
        Lancamento::factory()->create([
            'user_id' => $this->user->id,
            'categoria_saida' => CategoriaSaida::LAZER,
            'valor' => 100,
            'mes_referencia' => '2026-03-05'
        ]);

        // A soma deve ser apenas 150 + 50 = 200
        $this->assertEquals(200, $limite->soma_categoria);
    }
}