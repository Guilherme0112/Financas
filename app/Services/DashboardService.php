<?php

namespace App\Services;

use App\DTOs\Dashboard;
use App\DTOs\DashboardCards;
use App\Repositories\LancamentoRepository;
use Carbon\Carbon;

class DashboardService
{
    public function __construct(
        public LancamentoRepository $lancamentoRepository
    ) {
    }

    public function index()
    {
        $totais = $this->lancamentoRepository->obterTotalPorPeriodo(
            data_inicial: now()->startOfMonth(),
            data_final: now()->endOfMonth()
        );

        $lancamentosPorCategoria = $this->lancamentoRepository->obterTotaisDeCategoriasPorPeriodo(
            data_inicial: now()->startOfMonth(),
            data_final: now()->endOfMonth()
        );

        $lancamentosPorMes = $this->lancamentoRepository->obterTotaisMensaisPorPeriodo(
            data_inicial: now()->subMonths(5)->startOfMonth(),
            data_final: now()->endOfMonth()
        );

        $lancamentosPertoDeVencer = $this->lancamentoRepository->obterSaidasProximasDoVencimento(
            emXdias: Carbon::now()->addDays(7),
            limit: 5,
            foi_pago: false
        );

        $lancamentosVencidos = $this->lancamentoRepository->obterSaidasVencidasPorPeriodo(
            hoje: Carbon::now(),
            limit: 5
        );

        $dadosMes = $lancamentosPorMes->map(fn($item) => [
            Carbon::parse($item->mes)->format('Y-m'),
            (float) $item->entradas,
            (float) $item->saidas,
        ]);

        $porcentual = $this->calcularPorcentagemEmRelacaoAoMesAnterior(dadosMes: $dadosMes->toArray());

        $cards = new DashboardCards(
            entradas: (float) $totais->entradas,
            saidas: (float) $totais->saidas,
            total: (float) $totais->entradas - (float) $totais->saidas,
        );

        return new Dashboard(
            cards: $cards,
            graficos: [
                'pizza' => [
                    "gastos" => $lancamentosPorCategoria['gastos'],
                    "receitas" => $lancamentosPorCategoria['receitas'],
                ],
                'mensal' => $dadosMes,
            ],
            porcentual: $porcentual,
            lancamentos_perto_de_vencer: $lancamentosPertoDeVencer->toArray(),
            lancamentos_vencidos: $lancamentosVencidos->toArray(),
        );
    }

    private function calcularPorcentagemEmRelacaoAoMesAnterior(array $dadosMes)
    {
        $ultimo = count($dadosMes) - 1;
        $anterior = $ultimo - 1;
        $entradaAtual = $dadosMes[$ultimo][1] ?? 0;
        $entradaAnterior = $dadosMes[$anterior][1] ?? 0;
        $saidaAtual = $dadosMes[$ultimo][2] ?? 0;
        $saidaAnterior = $dadosMes[$anterior][2] ?? 0;

        return [
            'entradas' => $entradaAnterior > 0
                ? (($entradaAtual - $entradaAnterior) / $entradaAnterior) * 100
                : null,
            'saidas' => $saidaAnterior > 0
                ? (($saidaAtual - $saidaAnterior) / $saidaAnterior) * 100
                : null,
        ];
    }
}
