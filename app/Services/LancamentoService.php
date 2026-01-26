<?php

namespace App\Services;

use App\Enums\CategoriaEntrada;
use App\Enums\CategoriaSaida;
use App\Models\Lancamento;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class LancamentoService
{

    public function dashboard(): array
    {
        $data_inicial = Carbon::now()->startOfMonth()->toDateString();
        $data_final = Carbon::now()->endOfMonth()->toDateString();

        $entradas = Lancamento::where('tipo', 'ENTRADA')
            ->whereBetween('mes_referencia', [$data_inicial, $data_final])
            ->sum('valor');

        $saidas = Lancamento::where('tipo', 'SAIDA')
            ->whereBetween('mes_referencia', [$data_inicial, $data_final])
            ->sum('valor');

        $gastosPorCategoria = Lancamento::where('tipo', 'SAIDA')
            ->whereBetween('mes_referencia', [$data_inicial, $data_final])
            ->selectRaw('categoria_saida, SUM(valor) as total')
            ->groupBy('categoria_saida')
            ->orderByDesc('total')
            ->get();

        $receitasPorCategoria = Lancamento::where('tipo', 'ENTRADA')
            ->whereBetween('mes_referencia', [$data_inicial, $data_final])
            ->selectRaw('categoria_entrada, SUM(valor) as total')
            ->groupBy('categoria_entrada')
            ->orderByDesc('total')
            ->get();

        $gastosPorCategoria = $gastosPorCategoria->map(fn($item) => [
            'categoria' => ($item->categoria_saida instanceof CategoriaSaida)
                ? $item->categoria_saida->label()
                : CategoriaSaida::tryFrom($item->categoria_saida)?->label() ?? $item->categoria_saida,
            'total' => (float) $item->total,
        ]);

        $receitasPorCategoria = $receitasPorCategoria->map(fn($item) => [
            'categoria' => ($item->categoria_entrada instanceof CategoriaEntrada)
                ? $item->categoria_entrada->label()
                : CategoriaSaida::tryFrom($item->categoria_entrada)?->label() ?? $item->categoria_entrada,
            'total' => (float) $item->total,
        ]);

        $gastosPorMes = Lancamento::where('tipo', 'SAIDA')
            ->selectRaw("
                    date_trunc('month', mes_referencia) as mes,
                    SUM(valor) as total
                ")
            ->groupByRaw("date_trunc('month', mes_referencia)")
            ->orderBy('mes')
            ->get();


        $lancamentosPorMes = Lancamento::selectRaw("
            date_trunc('month', mes_referencia) as mes,
            SUM(CASE WHEN tipo = 'ENTRADA' THEN valor ELSE 0 END) as entradas,
            SUM(CASE WHEN tipo = 'SAIDA' THEN valor ELSE 0 END) as saidas
        ")
            ->groupByRaw("date_trunc('month', mes_referencia)")
            ->orderBy('mes')
            ->get();


        $dadosMes = $lancamentosPorMes->map(fn($item) => [
            Carbon::parse($item->mes)->format('Y-m'),
            (float) $item->entradas,
            (float) $item->saidas,
        ]);

        return [
            'cards' => [
                'entradas' => (float) $entradas,
                'saidas' => (float) $saidas,
                'total' => (float) ($entradas - $saidas),
            ],
            'graficos' => [
                'pizza' => [
                    "gastos" => $gastosPorCategoria,
                    "receitas" => $receitasPorCategoria
                ],
                'linha' => $gastosPorMes,
                'mensal' => $dadosMes,
            ],
        ];
    }


    public function listar(array $filtros = [], int $perPage = 10): LengthAwarePaginator
    {
        return Lancamento::query()
            ->when(
                isset($filtros['tipo']) && $filtros['tipo'] !== 'TODOS',
                function ($q) use ($filtros) {
                    $q->where('tipo', $filtros['tipo']);
                }
            )
            ->when($filtros['data_inicio'] ?? null, function ($q, $dataInicio) {
                $q->whereDate('mes_referencia', '>=', $dataInicio);
            })
            ->when($filtros['data_fim'] ?? null, function ($q, $dataFim) {
                $q->whereDate('mes_referencia', '<=', $dataFim);
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

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

    public function criar(array $dados): Lancamento
    {
        $this->validarTipoCategoria($dados);
        return Lancamento::create($dados);
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
