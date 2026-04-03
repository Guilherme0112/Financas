<?php

namespace App\Jobs;

use App\Contracts\GatewayPagamentoInterface;
use App\Enums\StatusAssinatura;
use App\Enums\StatusPagamento;
use App\Enums\TipoCobranca;
use App\Models\Fatura;
use App\Services\AssinaturaService;
use App\Services\FaturaService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessarFaturasDoMesJob implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(AssinaturaService $assinaturaService, GatewayPagamentoInterface $gatewayPagamentoInterface, FaturaService $faturaService)
    {
        $assinaturasPertoDoVencimento = $assinaturaService->obterDiasAntesDoVencimento(7, StatusAssinatura::ATIVA);

        if ($assinaturasPertoDoVencimento->isEmpty()) {
            logger()->info("Nenhuma assinatura para processar hoje.");
            return;
        }

        foreach ($assinaturasPertoDoVencimento as $assinatura) {
            try {
                $faturaExistente = Fatura::where('assinatura_id', $assinatura->id)
                    ->whereDate('vencimento_em', $assinatura->data_proxima_cobranca)
                    ->exists();

                if ($faturaExistente) {
                    logger()->info("Fatura já gerada para assinatura {$assinatura->id} no período {$assinatura->data_proxima_cobranca}");
                    continue;
                }

                logger()->info("Gerando fatura para: {$assinatura->user->name}");

                \DB::transaction(function () use ($faturaService, $assinatura, $gatewayPagamentoInterface) {
                    $fatura = $faturaService->criarFatura([
                        'user_id' => $assinatura->user_id,
                        'assinatura_id' => $assinatura->id,
                        'valor' => $assinatura->plano->preco,
                        'status' => StatusPagamento::PENDENTE,
                        'vencimento_em' => $assinatura->data_proxima_cobranca,
                        'periodo_inicio' => $assinatura->data_proxima_cobranca,
                        'periodo_fim' => $assinatura->data_proxima_cobranca->copy()->addMonthNoOverflow(), // Ajuste aqui
                        'url_pagamento' => null,
                        'referencia_externa' => null,
                        'metodo_pagamento' => null,
                        'tipo_cobranca' => TipoCobranca::CICLO_NORMAL
                    ], $assinatura->user_id);

                    $linkPagamento = $gatewayPagamentoInterface->criarPagamento($fatura);

                    $fatura->update([
                        'url_pagamento' => $linkPagamento["sandbox_init_point"],
                        'referencia_externa' => $linkPagamento["id"],
                    ]);

                    $assinatura->update([
                        'data_proxima_cobranca' => $assinatura->data_proxima_cobranca->addMonthNoOverflow()
                    ]);
                });
            } catch (\Throwable $th) {
                logger()->error("Erro ao processar assinatura {$assinatura->id}: " . $th->getMessage());
            }
        }

        logger()->info("Processamento finalizado com sucesso!");
    }
}
