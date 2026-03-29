<?php

namespace Tests\Feature\Services;

use App\Models\Lancamento;
use App\Models\Meta;
use App\Models\User;
use App\Services\LancamentoService;
use App\Repositories\LancamentoRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class LancamentoServiceTest extends TestCase
{
    use RefreshDatabase;

    private LancamentoService $lancamentoService;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->lancamentoService = app(LancamentoService::class);
        $this->user = User::factory()->create();
    }

    public function test_deve_criar_lancamento(): void
    {
        $dados = [
            'nome' => 'Salário',
            'tipo' => 'ENTRADA',
            'categoria_entrada' => 'SALARIO',
            'valor' => 100,
            'mes_referencia' => '2026-03-01',
        ];

        $resultado = $this->lancamentoService->criar($this->user->id, $dados);
        $this->assertCount(1, $resultado);
        $this->assertDatabaseHas('lancamentos', [
            'nome' => 'Salário',
            'valor' => 100,
            'user_id' => $this->user->id
        ]);
    }

    public function test_deve_falhar_ao_criar_lancamento_com_categoria_invalida(): void
    {
        $dados = [
            'nome' => 'Salário',
            'tipo' => 'ENTRADA',
            'categoria_entrada' => 'CATEGORIA_INVALIDA',
            'valor' => 100,
            'mes_referencia' => '2026-03-01',
        ];

        $this->expectException(ValidationException::class);
        $this->lancamentoService->criar($this->user->id, $dados);
    }

    public function test_deve_falhar_ao_criar_lancamento_com_tipo_invalido(): void
    {
        $dados = [
            'nome' => 'Salário',
            'tipo' => 'TIPO_INVALIDO',
            'categoria_entrada' => 'SALARIO',
            'valor' => 100,
            'mes_referencia' => '2026-03-01',
        ];

        $this->expectException(ValidationException::class);
        $this->lancamentoService->criar($this->user->id, $dados);
    }

    public function test_deve_criar_lancamento_com_meta(): void
    {
        $metaMock = Meta::factory()->create(['user_id' => $this->user->id]);

        $dados = [
            'nome' => 'Moto',
            'tipo' => 'RESERVA_META',
            'valor' => 50,
            'mes_referencia' => '2026-03-01',
            'meta_id' => $metaMock->id,
            'user_id' => $this->user->id
        ];

        $resultado = $this->lancamentoService->criar($this->user->id, $dados);
        $this->assertCount(1, $resultado);
        $this->assertDatabaseHas('lancamentos', [
            'nome' => 'Moto',
            'tipo' => 'RESERVA_META',
            'valor' => 50,
            'meta_id' => $metaMock->id,
            'user_id' => $this->user->id
        ]);
    }

    public function test_deve_falhar_ao_processar_quantidade_de_meses(): void
    {
        $dados = [
            'nome' => 'Salário',
            'tipo' => 'ENTRADA',
            'categoria_entrada' => 'SALARIO',
            'valor' => 100,
            'mes_referencia' => '2026-03-01',
            'meses_recorrentes' => 0
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->lancamentoService->criar($this->user->id, $dados);
    }

    public function test_deve_falhar_ao_criar_com_tipo_certo_e_categoria_incorreta(): void
    {
        $dados = [
            'nome' => 'Salário',
            'tipo' => 'ENTRADA',
            'categoria_entrada' => 'ALIMENTACAO', // ALIMENTACAO é válido somente em CategoriaSaida
            'valor' => 100,
            'mes_referencia' => '2026-03-01',
        ];

        $this->expectException(ValidationException::class);
        $this->lancamentoService->criar($this->user->id, $dados);
    }

    public function test_deve_atualizar_lancamento(): void
    {
        $lancamento = Lancamento::factory()->create(['valor' => 100, 'user_id' => $this->user->id]);

        $resultado = $this->lancamentoService->atualizar($lancamento->id, $this->user->id, [
            'nome' => 'Salário Atualizado',
            'tipo' => 'ENTRADA',
            'categoria_entrada' => 'SALARIO',
            'valor' => 150,
        ]);

        $this->assertEquals(150, $resultado->valor);
        $this->assertDatabaseHas('lancamentos', [
            'id' => $lancamento->id,
            'nome' => 'Salário Atualizado',
            'valor' => 150
        ]);
    }

    public function test_deve_falhar_ao_atualizar_lancamento_de_outro_usuario(): void
    {
        $user2 = User::factory()->create();
        $lancamento = Lancamento::factory()->create(['valor' => 100, 'user_id' => $this->user->id]);

        $this->expectException(ModelNotFoundException::class);

        $this->lancamentoService->atualizar($lancamento->id, $user2->id, [
            'nome' => 'Salário Atualizado',
            'tipo' => 'ENTRADA',
            'categoria_entrada' => 'SALARIO',
            'valor' => 150,
        ]);
    }

    public function test_deve_atualizar_lancamento_com_meta(): void
    {
        $metaMock = Meta::factory()->create(['user_id' => $this->user->id]);
        $lancamento = Lancamento::factory()->create(['valor' => 100, 'user_id' => $this->user->id]);

        $resultado = $this->lancamentoService->atualizar($lancamento->id, $this->user->id, [
            'tipo' => 'RESERVA_META',
            'valor' => 150,
            'meta_id' => $metaMock->id
        ]);

        $this->assertEquals(150, $resultado->valor);
        $this->assertDatabaseHas('lancamentos', [
            'id' => $lancamento->id,
            'valor' => 150,
            'meta_id' => $metaMock->id
        ]);
    }

    public function test_deve_deletar_lancamento_com_sucesso(): void
    {
        $user = User::factory()->create();
        $lancamento = Lancamento::factory()->create(['user_id' => $user->id]);

        $service = new LancamentoService(new LancamentoRepository());
        $service->deletar($lancamento->id, $user->id);

        $this->assertDatabaseMissing('lancamentos', [
            'id' => $lancamento->id
        ]);
    }

    public function test_deve_falhar_ao_deletar_lancamento_de_outro_usuario(): void
    {
        $user2 = User::factory()->create();
        $lancamento = Lancamento::factory()->create(['user_id' => $this->user->id]);

        $this->expectException(ModelNotFoundException::class);

        $this->lancamentoService->deletar($lancamento->id, $user2->id);
    }
}