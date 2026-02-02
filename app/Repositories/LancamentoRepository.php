<?php

namespace App\Repositories;

use App\Enums\TipoValor;
use App\Models\Lancamento;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class LancamentoRepository
{

    public function obterLancamentos(array $filtros, int $perPage)
    {
        return Lancamento::query()
            ->when(
                ($filtros['tipo'] ?? null) && $filtros['tipo'] !== 'TODOS',
                fn($q) => $q->whereTipo($filtros['tipo'])
            )
            ->when(
                $filtros['categoria_entrada'] ?? null,
                fn($q) => $q->whereCategoriaEntrada($filtros['categoria_entrada'])
            )
            ->when(
                $filtros['categoria_saida'] ?? null,
                fn($q) => $q->whereCategoriaSaida($filtros['categoria_saida'])
            )
            ->when(
                $filtros['data_inicio'] ?? null,
                fn($q) => $q->whereDate('mes_referencia', '>=', $filtros['data_inicio'])
            )
            ->when(
                $filtros['data_fim'] ?? null,
                fn($q) => $q->whereDate('mes_referencia', '<=', $filtros['data_fim'])
            )
            ->when(
                $filtros['foi_pago'] ?? null,
                fn($q) => $q->whereFoiPago($filtros['foi_pago'])
            )
            ->when(
                $filtros['recorrentes'] ?? null,
                fn($q) => $q->whereRecorrente($filtros['recorrentes'])
            )
            ->latest()
            ->paginate($perPage);
    }

    public function obterTotalPorPeriodo($data_inicial, $data_final): Lancamento
    {
        return Lancamento::select([])
            ->selectRaw(
                "SUM(CASE WHEN tipo::text = ? THEN valor ELSE 0.00 END) as entradas,
                            SUM(CASE WHEN tipo::text = ? THEN valor ELSE 0.00 END) as saidas",
                [TipoValor::ENTRADA->value, TipoValor::SAIDA->value]
            )
            ->whereBetween('mes_referencia', [$data_inicial, $data_final])
            ->first();
    }

    public function obterTotaisMensaisPorPeriodo($data_inicial, $data_final): Collection
    {
        return Lancamento::selectRaw(
            "date_trunc('month', mes_referencia) as mes,
        SUM(CASE WHEN tipo::text = ? THEN valor ELSE 0.00 END) as entradas,
        SUM(CASE WHEN tipo::text = ? THEN valor ELSE 0.00 END) as saidas",
            [TipoValor::ENTRADA->value, TipoValor::SAIDA->value]
        )
            ->whereBetween('mes_referencia', [$data_inicial, $data_final])
            ->groupByRaw("date_trunc('month', mes_referencia)")
            ->orderBy('mes')
            ->get();
    }

    public function obterSaidasProximasDoVencimento(Carbon $emXdias, int $limit, bool $foi_pago = true): Collection
    {
        return Lancamento::where('tipo', 'SAIDA')
            ->whereBetween('mes_referencia', [now()->startOfDay(), $emXdias])
            ->whereFoiPago($foi_pago)
            ->orderBy('mes_referencia')
            ->limit($limit)
            ->get();
    }

    public function obterSaidasVencidasPorPeriodo(Carbon $hoje, int $limit): Collection
    {
        return Lancamento::whereDate('mes_referencia', '<', $hoje)
            ->whereTipo(TipoValor::SAIDA)
            ->whereFoiPago(false)
            ->limit($limit)
            ->orderByDesc('mes_referencia')
            ->get();
    }

    public function obterTotaisDeCategoriasPorPeriodo(Carbon $data_inicial, Carbon $data_final)
    {
        $baseMesAtual = Lancamento::whereBetween(
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
            "gastos" => $gastosPorCategoria,
            "receitas" => $receitasPorCategoria
        ];
    }

}
