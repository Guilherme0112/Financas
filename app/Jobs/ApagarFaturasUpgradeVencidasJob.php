<?php

namespace App\Jobs;

use App\Enums\StatusSolicitacaoMudancaPlano;
use App\Models\Fatura;
use App\Enums\StatusPagamento;
use App\Enums\TipoCobranca;
use DB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ApagarFaturasUpgradeVencidasJob implements ShouldQueue
{
    use Queueable;

    /**
     * Execute o job.
     */
    public function handle(): void
    {
        $faturasParaRemover = Fatura::where('tipo_cobranca', TipoCobranca::UPGRADE)
            ->where('status', StatusPagamento::PENDENTE)
            ->where('vencimento_em', '<', now())
            ->get();

        if ($faturasParaRemover->isEmpty()) {
            return;
        }

        foreach ($faturasParaRemover as $fatura) {
            DB::transaction(function () use ($fatura) {
                $fatura->solicitacoesMudancaPlanos()
                    ->where('status', StatusSolicitacaoMudancaPlano::PENDENTE)
                    ->update(['status' => StatusSolicitacaoMudancaPlano::CANCELADO]);
                $fatura->delete();
                logger()->info("Fatura de Upgrade ID {$fatura->id} removida por vencimento.");
            });
        }
    }
}