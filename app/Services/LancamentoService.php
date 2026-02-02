<?php

namespace App\Services;

use App\Enums\CategoriaEntrada;
use App\Enums\CategoriaSaida;
use App\Http\Requests\IndexLancamentosRequest;
use App\Models\Lancamento;
use App\Repositories\LancamentoRepository;
use Carbon\Carbon;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class LancamentoService
{
    public function __construct(
        public LancamentoRepository $lancamentoRepository
    ) { }

    public function listar(IndexLancamentosRequest $filtros, int $perPage = 15): LengthAwarePaginator
    {
        return $this->lancamentoRepository->obterLancamentos($filtros->toArray(), $perPage);
    }

    // todo: a entidade deve se validar
    private function validarTipoCategoria(array $dados): void
    {
        if ($dados['tipo'] === "ENTRADA") {
            try {
                CategoriaEntrada::from($dados['categoria_entrada']);
                return;
            } catch (ValidationException $th) {
                throw ValidationException::withMessages([
                    'categoria_entrada' => 'Categoria de entrada inválida para o tipo selecionado.'
                ]);
            }
        }

        if ($dados['tipo'] === "SAIDA") {
            try {
                CategoriaSaida::from($dados['categoria_saida']);
                return;
            } catch (ValidationException $th) {
                throw ValidationException::withMessages([
                    'categoria_saida' => 'Categoria de saída inválida para o tipo selecionado.'
                ]);
                ;
            }
        }
    }

    public function criar(array $dados): Collection
    {
        $this->validarTipoCategoria($dados);
        $quantidadeMeses = (int) ($dados['meses_recorrentes'] ?? 1);
        $dataBase = Carbon::parse($dados['mes_referencia']);
        unset($dados['meses_recorrentes']);

        $lancamentos = collect();
        DB::transaction(function () use ($quantidadeMeses, $dataBase, $dados, &$lancamentos) {
            for ($i = 0; $i < $quantidadeMeses; $i++) {
                $dados['mes_referencia'] = $dataBase
                    ->copy()
                    ->addMonths($i);

                $lancamentos->push(
                    Lancamento::create($dados)
                );
            }
        });

        return $lancamentos;
    }

    public function atualizar(string $id, array $dados): Lancamento
    {
        $lancamento = Lancamento::findOrFail($id);
        $this->validarTipoCategoria($dados);
        $lancamento->update($dados);
        return $lancamento;
    }

    public function deletar(string $id): void
    {
        $lancamento = Lancamento::findOrFail($id);
        $lancamento->delete();
    }
}
