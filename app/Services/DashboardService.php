<?php

namespace App\Services;

use App\DTOs\Dashboard;
use App\DTOs\DashboardCards;
use App\Repositories\LancamentoRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardService
{
    public function __construct(
        public LancamentoRepository $lancamentoRepository,
        public LimiteCategoriaService $limiteCategoriaService,
        public MetasService $metaService
    ) {
    }

    public function index(int $userId)
    {
        $mesAtual = now()->startOfMonth();
        $fimMesAtual = now()->endOfMonth();

        $totais = $this->lancamentoRepository->obterTotalPorPeriodo(
            data_inicial: $mesAtual,
            data_final: $fimMesAtual,
            userId: $userId
        );

        $lancamentosPorCategoria = $this->lancamentoRepository->obterTotaisDeCategoriasPorPeriodo(
            data_inicial: $mesAtual,
            data_final: $fimMesAtual,
            userId: $userId
        );

        $lancamentosPorMes = $this->lancamentoRepository->obterTotaisMensaisPorPeriodo(
            data_inicial: now()->subMonths(5)->startOfMonth(),
            data_final: $fimMesAtual,
            userId: $userId
        );

        $lancamentosPertoDeVencer = $this->lancamentoRepository->obterSaidasProximasDoVencimento(
            emXdias: Carbon::now()->addDays(7),
            limit: 5,
            foi_pago: false,
            userId: $userId
        );

        $lancamentosVencidos = $this->lancamentoRepository->obterSaidasVencidasPorPeriodo(
            hoje: Carbon::now(),
            limit: 5,
            userId: $userId
        );

        $dadosMes = $lancamentosPorMes->map(fn($item) => [
            Carbon::parse($item->mes)->format('Y-m'),
            (float) $item->entradas,
            (float) $item->saidas,
        ]);

        $comparacao = $this->calcularComparacaoMesAnterior(dadosMes: $dadosMes);

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
            porcentual: $comparacao,
            lancamentos_perto_de_vencer: $lancamentosPertoDeVencer->toArray(),
            lancamentos_vencidos: $lancamentosVencidos->toArray(),
            limites: $this->limiteCategoriaService->listar([], $userId, 6),
            metas: $this->metaService->listar([], $userId, 3)
        );
    }

    private function calcularComparacaoMesAnterior(Collection $dadosMes): array
    {
        $mesAtual = now()->format('Y-m');
        $mesAnterior = now()->subMonth()->format('Y-m');

        $dadosIndexados = $dadosMes->keyBy(fn($item) => $item[0]);

        $entradaAtual = $dadosIndexados[$mesAtual][1] ?? 0;
        $entradaAnterior = $dadosIndexados[$mesAnterior][1] ?? 0;

        $saidaAtual = $dadosIndexados[$mesAtual][2] ?? 0;
        $saidaAnterior = $dadosIndexados[$mesAnterior][2] ?? 0;

        return [
            'entradas' => $this->compararValores($entradaAtual, $entradaAnterior),
            'saidas' => $this->compararValores($saidaAtual, $saidaAnterior),
        ];
    }

    private function compararValores(float $atual, float $anterior): array
    {
        $diferenca = $atual - $anterior;

        $percentual = $anterior != 0
            ? ($diferenca / $anterior) * 100
            : null;

        return [
            'atual' => $atual,
            'anterior' => $anterior,
            'diferenca' => $diferenca,
            'percentual' => $percentual,
            'tendencia' => $diferenca > 0
                ? 'up'
                : ($diferenca < 0 ? 'down' : 'stable'),
        ];
    }


}
