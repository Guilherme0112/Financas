<?php

namespace App\Services;

use App\Enums\CategoriaEntrada;
use App\Enums\CategoriaSaida;
use App\Models\Lancamento;
use Carbon\Carbon;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class LancamentoService
{

    public function dashboard(): array
    {
        $data_inicial = Carbon::now()->startOfMonth()->toDateString();
        $data_final = Carbon::now()->endOfMonth()->toDateString();

        // Calcular totais de entradas e saídas do mês da var
        $totais = Lancamento::whereBetween('mes_referencia', [$data_inicial, $data_final])
            ->selectRaw("SUM(CASE WHEN tipo = 'ENTRADA' THEN valor ELSE 0 END) as entradas,
                        SUM(CASE WHEN tipo = 'SAIDA' THEN valor ELSE 0 END) as saidas")
            ->first();


        // Busca logo os dados de uma vez e agrupa depois
        $baseMesAtual = Lancamento::whereBetween(
            'mes_referencia',
            [$data_inicial, $data_final]
        );
        $gastosPorCategoria = (clone $baseMesAtual)
            ->where('tipo', 'SAIDA')
            ->selectRaw('categoria_saida, SUM(valor) as total')
            ->groupBy('categoria_saida')
            ->orderByDesc('total')
            ->get();
        $receitasPorCategoria = (clone $baseMesAtual)
            ->where('tipo', 'ENTRADA')
            ->selectRaw('categoria_entrada, SUM(valor) as total')
            ->groupBy('categoria_entrada')
            ->orderByDesc('total')
            ->get();


        // Formatar os dados para o gráfico de pizza
        $gastosPorCategoria = $gastosPorCategoria->map(fn($item) => [
            'categoria' => ($item->categoria_saida instanceof CategoriaSaida)
                ? $item->categoria_saida->label()
                : CategoriaSaida::tryFrom($item->categoria_saida)?->label() ?? $item->categoria_saida,
            'total' => (float) $item->total,
        ]);
        $receitasPorCategoria = $receitasPorCategoria->map(fn($item) => [
            'categoria' => ($item->categoria_entrada instanceof CategoriaEntrada)
                ? $item->categoria_entrada->label()
                : CategoriaEntrada::tryFrom($item->categoria_entrada)?->label() ?? $item->categoria_entrada,
            'total' => (float) $item->total,
        ]);

        $inicio = now()->subMonths(5)->startOfMonth();
        $fim = now()->endOfMonth();
        $lancamentosPorMes = Lancamento::selectRaw("
        date_trunc('month', mes_referencia) as mes,
        SUM(CASE WHEN tipo = 'ENTRADA' THEN valor ELSE 0 END) as entradas,
        SUM(CASE WHEN tipo = 'SAIDA' THEN valor ELSE 0 END) as saidas
        ")
            ->whereBetween('mes_referencia', [$inicio, $fim])
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
                'entradas' => (float) $totais->entradas,
                'saidas' => (float) $totais->saidas,
                'total' => (float) ($totais->entradas - $totais->saidas),
            ],
            'graficos' => [
                'pizza' => [
                    "gastos" => $gastosPorCategoria,
                    "receitas" => $receitasPorCategoria
                ],
                'mensal' => $dadosMes,
            ],
        ];
    }


    public function listar(array $filtros = [], int $perPage = 15): LengthAwarePaginator
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
