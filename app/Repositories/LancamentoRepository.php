<?php

namespace App\Repositories;

use App\Enums\TipoValor;
use App\Models\Lancamento;
use Arr;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as EloquentCollection;

class LancamentoRepository
{
    public function obterLancamentos(array $filtros, ?int $perPage, int $userId): array
    {
        $query = Lancamento::query()
            ->with('meta')
            ->where('user_id', $userId)
            ->when(
                ($filtros['tipo'] ?? null) && $filtros['tipo'] !== 'TODOS',
                fn ($q) => $q->whereTipo($filtros['tipo'])
            )
            ->when(
                $filtros['categoria_entrada'] ?? null,
                fn ($q) => $q->whereCategoriaEntrada($filtros['categoria_entrada'])
            )
            ->when(
                $filtros['categoria_saida'] ?? null,
                fn ($q) => $q->whereCategoriaSaida($filtros['categoria_saida'])
            )
            ->when(
                Arr::get($filtros, 'data_inicio', Carbon::now()->startOfMonth()),
                fn ($q, $dataInicio) => $q->whereDate('mes_referencia', '>=', $dataInicio)
            )
            ->when(
                Arr::get($filtros, 'data_fim', Carbon::now()->endOfMonth()),
                fn ($q, $dataFim) => $q->whereDate('mes_referencia', '<=', $dataFim)
            )
            ->when($filtros['foi_pago'] ?? null, fn ($q, $v) => $q->where('foi_pago', filter_var($v, FILTER_VALIDATE_BOOLEAN)))
            ->when(
                $filtros['recorrentes'] ?? null,
                fn ($q) => $q->whereRecorrente($filtros['recorrentes'])
            );

        $totais = (clone $query)
            ->selectRaw("
                    SUM(CASE WHEN tipo = 'ENTRADA' THEN valor ELSE 0 END) as total_entradas,
                    SUM(CASE WHEN tipo = 'SAIDA' THEN valor ELSE 0 END) as total_saidas,
                    SUM(CASE WHEN tipo = 'RESERVA_META' THEN valor ELSE 0 END) as total_reserva_meta
                ")
            ->first();

        $lancamentos = $query
            ->latest()
            ->paginate($perPage);

        return [
            'paginacao' => $lancamentos,
            'resumo' => [
                'total_entradas' => (float) ($totais->total_entradas ?? 0),
                'total_saidas' => (float) ($totais->total_saidas ?? 0),
                'total_reserva_meta' => (float) ($totais->total_reserva_meta ?? 0),
                'saldo' => (float) (($totais->total_entradas ?? 0) - (($totais->total_saidas + $totais->total_reserva_meta) ?? 0)),
            ],
        ];
    }

    public function obterLancamentosKanban(array $filtros, int $perPage, int $userId): array
    {
        // 1. Query Base (Sem filtrar o tipo)
        $queryBase = Lancamento::query()
            ->with('meta')
            ->where('user_id', $userId)
            ->when(Arr::get($filtros, 'data_inicio'), fn ($q, $d) => $q->whereDate('mes_referencia', '>=', $d))
            ->when(Arr::get($filtros, 'data_fim'), fn ($q, $d) => $q->whereDate('mes_referencia', '<=', $d))
            ->when($filtros['foi_pago'] ?? null, fn ($q, $v) => $q->where('foi_pago', filter_var($v, FILTER_VALIDATE_BOOLEAN)))
            ->when($filtros['recorrentes'] ?? null, fn ($q, $v) => $q->whereRecorrente($v));

        // 2. Calcula os totais (útil pro cabeçalho de cada coluna)
        $totais = (clone $queryBase)->selectRaw("
        SUM(CASE WHEN tipo = 'ENTRADA' THEN valor ELSE 0 END) as entradas,
        SUM(CASE WHEN tipo = 'SAIDA' THEN valor ELSE 0 END) as saidas,
        SUM(CASE WHEN tipo = 'RESERVA_META' THEN valor ELSE 0 END) as metas
    ")->first();

        // 3. Paginações Independentes (O segredo está no 3º parâmetro: o pageName)
        $entradas = (clone $queryBase)->whereTipo('ENTRADA')->latest()->paginate($perPage, ['*'], 'page_entradas');
        $saidas = (clone $queryBase)->whereTipo('SAIDA')->latest()->paginate($perPage, ['*'], 'page_saidas');
        $metas = (clone $queryBase)->whereTipo('RESERVA_META')->latest()->paginate($perPage, ['*'], 'page_metas');

        // 4. Monta o Array no formato que o Kanban espera no Frontend
        $kanbanColumns = [
            [
                'id' => 'entradas',
                'nome_coluna' => 'Entradas',
                'soma_valores' => (float) ($totais->entradas ?? 0),
                'paginacao' => $entradas,
            ],
            [
                'id' => 'saidas',
                'nome_coluna' => 'Saídas',
                'soma_valores' => (float) ($totais->saidas ?? 0),
                'paginacao' => $saidas,
            ],
            [
                'id' => 'metas',
                'nome_coluna' => 'Reservas para Metas',
                'soma_valores' => (float) ($totais->metas ?? 0),
                'paginacao' => $metas,
            ],
        ];

        return [
            'kanban' => $kanbanColumns,
            'resumo' => [
                'total_entradas' => (float) ($totais->entradas ?? 0),
                'total_saidas' => (float) ($totais->saidas ?? 0),
                'total_reserva_meta' => (float) ($totais->metas ?? 0),
                'saldo' => (float) (($totais->entradas ?? 0) - (($totais->saidas + $totais->metas) ?? 0)),
            ],
        ];
    }

    public function obterPorIdAndUserId(int $id, int $userId): Lancamento
    {
        return Lancamento::where('user_id', $userId)->findOrFail($id);
    }

    public function obterTotalPorPeriodo($data_inicial, $data_final, int $userId): Lancamento
    {
        return Lancamento::select([])
            ->where('user_id', $userId)
            ->selectRaw(
                'SUM(CASE WHEN tipo::text = ? THEN valor ELSE 0.00 END) as entradas,
                            SUM(CASE WHEN tipo::text = ? THEN valor ELSE 0.00 END) as saidas,
                            SUM(CASE WHEN tipo::text = ? THEN valor ELSE 0.00 END) as reserva_meta',
                [TipoValor::ENTRADA->value, TipoValor::SAIDA->value, TipoValor::RESERVA_META->value]
            )
            ->whereBetween('mes_referencia', [$data_inicial, $data_final])
            ->first();
    }

    public function obterTotaisMensaisPorPeriodo($data_inicial, $data_final, int $userId): Collection
    {
        return Lancamento::selectRaw(
            "date_trunc('month', mes_referencia) as mes,
        SUM(CASE WHEN tipo::text = ? THEN valor ELSE 0.00 END) as entradas,
        SUM(CASE WHEN tipo::text = ? THEN valor ELSE 0.00 END) as saidas,
        SUM(CASE WHEN tipo::text = ? THEN valor ELSE 0.00 END) as reserva_meta",
            [TipoValor::ENTRADA->value, TipoValor::SAIDA->value, TipoValor::RESERVA_META->value]
        )
            ->where('user_id', $userId)
            ->whereBetween('mes_referencia', [$data_inicial, $data_final])
            ->groupByRaw("date_trunc('month', mes_referencia)")
            ->orderBy('mes')
            ->get();
    }

    public function obterSaidasProximasDoVencimento(Carbon $emXdias, int $limit, bool $foi_pago, int $userId): Collection
    {
        return Lancamento::where('tipo', 'SAIDA')
            ->where('user_id', $userId)
            ->whereBetween('mes_referencia', [now()->startOfDay(), $emXdias])
            ->whereFoiPago($foi_pago)
            ->orderBy('mes_referencia')
            ->limit($limit)
            ->get();
    }

    public function obterSaidasVencidasPorPeriodo(Carbon $hoje, int $limit, int $userId): Collection
    {
        return Lancamento::whereDate('mes_referencia', '<', $hoje)
            ->where('user_id', $userId)
            ->whereTipo(TipoValor::SAIDA)
            ->whereFoiPago(false)
            ->limit($limit)
            ->orderByDesc('mes_referencia')
            ->get();
    }

    public function obterTotaisDeCategoriasPorPeriodo(Carbon $data_inicial, Carbon $data_final, int $userId): array
    {
        $baseMesAtual = Lancamento::where('user_id', $userId)
            ->whereBetween(
                'mes_referencia',
                [$data_inicial, $data_final]
            );

        $gastosPorCategoria = (clone $baseMesAtual)
            ->whereTipo('SAIDA')
            ->selectRaw('tipo, categoria_saida, SUM(valor) as total')
            ->groupBy('tipo', 'categoria_saida')
            ->orderByDesc('total')
            ->get();
        $receitasPorCategoria = (clone $baseMesAtual)
            ->whereTipo('ENTRADA')
            ->selectRaw('tipo, categoria_entrada, SUM(valor) as total')
            ->groupBy('tipo', 'categoria_entrada')
            ->orderByDesc('total')
            ->get();

        return [
            'gastos' => $gastosPorCategoria,
            'receitas' => $receitasPorCategoria,
        ];
    }

    public function criarVarios(array $dados): EloquentCollection
    {
        Lancamento::insert($dados);

        return collect($dados);
    }

    public function atualizar(int $id, array $dados): Lancamento
    {
        $lancamento = $this->obterPorIdAndUserId($id, $dados['user_id']);
        $lancamento->update($dados);

        return $lancamento;
    }

    public function marcarComoPaga(int $id, int $userId): Lancamento
    {
        $lancamento = $this->obterPorIdAndUserId($id, $userId);
        $lancamento->update(['foi_pago' => true, 'data_quitacao' => now()]);

        return $lancamento;
    }

    public function deletarVarios(array $ids, int $userId): int
    {
        return Lancamento::whereIn('id', $ids)
            ->where('user_id', $userId)
            ->delete();
    }
}
