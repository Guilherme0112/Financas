<?php

namespace App\Services;

use App\Enums\CategoriaEntrada;
use App\Enums\CategoriaSaida;
use App\Enums\TipoValor;
use App\Models\Lancamento;
use App\Repositories\LancamentoRepository;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class LancamentoService
{
    public function __construct(
        public LancamentoRepository $lancamentoRepository
    ) {
    }

    public function listar(array $filtros, ?int $perPage = 20, int $userId): array
    {
        return $this->lancamentoRepository->obterLancamentos($filtros, $perPage, $userId);
    }

    public function obterTotaisMensaisPorPeriodo($data_inicial, $data_final, int $userId): Collection
    {
        return $this->lancamentoRepository->obterTotaisMensaisPorPeriodo($data_inicial, $data_final, $userId);
    }

    // TODO: a entidade deve se validar
    private function validarTipoCategoria(array $dados): void
    {

        if (!TipoValor::tryFrom($dados['tipo'])) {
            throw ValidationException::withMessages([
                'tipo' => 'Tipo de lançamento inválido.'
            ]);
        }

        if ($dados['tipo'] === "ENTRADA") {
            if (!CategoriaEntrada::tryFrom($dados['categoria_entrada'])) {
                throw ValidationException::withMessages([
                    'categoria_entrada' => 'Categoria de entrada inválida para o tipo selecionado.'
                ]);
            }
        }

        if ($dados['tipo'] === "SAIDA") {
            if (!CategoriaSaida::tryFrom($dados['categoria_saida'])) {
                throw ValidationException::withMessages([
                    'categoria_saida' => 'Categoria de saída inválida para o tipo selecionado.'
                ]);
            }
        }
    }

    public function criar(int $userId, array $dados): Collection
    {
        $this->validarTipoCategoria($dados);

        $quantidadeMeses = (int) ($dados['meses_recorrentes'] ?? 1);
        unset($dados['meses_recorrentes']);

        if ($quantidadeMeses < 1) {
            throw new \InvalidArgumentException("A quantidade de meses deve ser pelo menos 1.");
        }

        $dataBase = Carbon::parse($dados['mes_referencia']);
        $valorAbsoluto = abs($dados['valor']);

        $payloads = [];
        for ($i = 0; $i < $quantidadeMeses; $i++) {
            $payloads[] = array_merge($dados, [
                'user_id' => $userId,
                'valor' => $valorAbsoluto,
                'mes_referencia' => $dataBase->copy()->addMonths($i)->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return DB::transaction(function () use ($payloads) {
            return $this->lancamentoRepository->criarVarios($payloads);
        });
    }

    public function atualizar(int $id, int $userId, array $dados): Lancamento
    {
        $lancamento = $this->lancamentoRepository->obterPorIdAndUserId($id, $userId);
        $this->validarTipoCategoria($dados);
        $lancamento->update($dados);
        return $lancamento;
    }

    public function marcarComoPaga(int $id, int $userId): Lancamento
    {
        return $this->lancamentoRepository->marcarComoPaga($id, $userId);
    }

    public function deletar(string $id, int $userId): void
    {
        $lancamento = $this->lancamentoRepository->obterPorIdAndUserId($id, $userId);
        $lancamento->delete();
    }

    public function deletarVarios(array $ids, int $userId): int
    {
        return $this->lancamentoRepository->deletarVarios($ids, $userId);
    }
}
