<?php

namespace Tests\Feature\Services;

use App\Models\Meta;
use App\Models\User;
use App\Repositories\MetasRepository;
use App\Services\MetasService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MetaServiceTest extends TestCase
{
    use RefreshDatabase;

    private MetasService $metaService;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->metaService = new MetasService(new MetasRepository());
        $this->user = User::factory()->create();
    }

    public function test_deve_criar_meta(): void
    {
        $payload = [
            "nome" => "Meta de Viagem",
            "valor_objetivo" => 5000,
            "ate_quando" => "2026-12-31",
            "user_id" => $this->user->id
        ];

        $resposta = $this->metaService->criar($payload, $this->user->id);
        $this->assertInstanceOf(Meta::class, $resposta);
        $this->assertDatabaseHas('metas', [
            'nome' => 'Meta de Viagem',
            'valor_objetivo' => 5000,
            'ate_quando' => "2026-12-31",
            'user_id' => $this->user->id
        ]);
    }

    public function test_deve_atualizar_meta(): void
    {
        $meta = Meta::factory()->create(['user_id' => $this->user->id]);

        $payload = [
            "nome" => "Meta de Carro",
            "valor_objetivo" => 20000,
            "ate_quando" => "2025-06-30",
            "user_id" => $this->user->id
        ];

        $resposta = $this->metaService->atualizar($meta->id, $payload, $this->user->id);
        $this->assertInstanceOf(Meta::class, $resposta);
        $this->assertDatabaseHas('metas', [
            'id' => $meta->id,
            'nome' => 'Meta de Carro',
            'valor_objetivo' => 20000,
            'ate_quando' => "2025-06-30",
            'user_id' => $this->user->id
        ]);
    }

    public function test_deve_falhar_ao_atualizar_meta_de_outro_usuario(): void
    {
        $outroUsuario = User::factory()->create();
        $meta = Meta::factory()->create(['user_id' => $outroUsuario->id]);

        $payload = [
            "nome" => "Meta de Carro",
            "valor_objetivo" => 20000,
            "ate_quando" => "2025-06-30",
            "user_id" => $this->user->id
        ];

        $this->expectException(ModelNotFoundException::class);
        $this->metaService->atualizar($meta->id, $payload, $this->user->id);
    }

    public function test_deve_excluir_meta(): void
    {
        $meta = Meta::factory()->create(['user_id' => $this->user->id]);

        $this->metaService->excluir($meta->id, $this->user->id);
        $this->assertDatabaseMissing('metas', [
            'id' => $meta->id,
            'user_id' => $this->user->id
        ]);
    }

    public function test_deve_falhar_ao_excluir_meta_de_outro_usuario(): void
    {
        $outroUsuario = User::factory()->create();
        $meta = Meta::factory()->create(['user_id' => $outroUsuario->id]);

        $this->expectException(ModelNotFoundException::class);
        $this->metaService->excluir($meta->id, $this->user->id);
    }
}
