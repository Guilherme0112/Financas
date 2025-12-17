<?php

namespace App\Services;

use App\Models\Lancamento;
use Carbon\Carbon;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GestaoService
{

    public function dashboard(): array
    {
        $inicioMes = Carbon::now()->startOfMonth();
        $fimMes = Carbon::now()->endOfMonth();

        // Entradas do mês
        $entradasMes = Lancamento::where('tipo', 'ENTRADA')
            ->whereBetween('mes_referencia', [$inicioMes, $fimMes])
            ->sum('valor');

        // Saídas do mês
        $saidasMes = Lancamento::where('tipo', 'SAIDA')
            ->whereBetween('mes_referencia', [$inicioMes, $fimMes])
            ->sum('valor');

        // Saldo
        $saldoAtual = $entradasMes - $saidasMes;

        // Pizza - gastos por categoria
        $gastosPorCategoria = Lancamento::select(
            'categorias.id as categoria_id',
            'categorias.nome as categoria',
            DB::raw('SUM(lancamentos.valor) as total')
        )
            ->join('categorias', 'categorias.id', '=', 'lancamentos.categoria_id')
            ->where('lancamentos.tipo', 'SAIDA')
            ->groupBy('categorias.id', 'categorias.nome')
            ->get();

        // Linha - evolução mensal
        $evolucaoMensal = Lancamento::select(
            DB::raw("DATE_TRUNC('month', mes_referencia) as mes"),
            DB::raw('SUM(valor) as total')
        )
            ->where('tipo', 'SAIDA')
            ->groupBy(DB::raw("DATE_TRUNC('month', mes_referencia)"))
            ->orderBy('mes')
            ->get();

        // Barra - fixos vs variáveis
        $fixos = Lancamento::where('tipo', 'SAIDA')
            ->where('recorrente', true)
            ->sum('valor');

        $variaveis = Lancamento::where('tipo', 'SAIDA')
            ->where('recorrente', false)
            ->sum('valor');

        return [
            'cards' => [
                'entradas' => (float) $entradasMes,
                'saidas' => (float) $saidasMes,
                'total' => (float) $saldoAtual,
            ],
            'graficos' => [
                'pizza' => $gastosPorCategoria,
                'linha' => $evolucaoMensal,
                'barra' => [
                    'fixos' => (float) $fixos,
                    'variaveis' => (float) $variaveis,
                ],
            ],
        ];
    }


    public function listar(int $perPage = 10): LengthAwarePaginator
    {
        return Lancamento::query()
            ->latest()
            ->paginate($perPage);
    }

    public function criar(array $dados): Lancamento
    {
        return Lancamento::create($dados);
    }

    public function atualizar(string $id, array $dados): Lancamento
    {
        $lancamento = Lancamento::findOrFail($id);
        $lancamento->update($dados);

        return $lancamento;
    }

    public function deletar(string $id): void
    {
        $lancamento = Lancamento::findOrFail($id);
        $lancamento->delete();
    }
}
