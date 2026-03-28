<?php

namespace App\Jobs;

use App\Enums\StatusPagamento;
use App\Enums\TipoCobranca;
use App\Models\Fatura;
use App\Services\AssinaturaService;
use App\Services\FaturaService;
use App\Services\MercadoPagoService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessarFaturasDoMesJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(AssinaturaService $assinaturaService, MercadoPagoService $mercadoPagoService, FaturaService $faturaService)
    {
        // Buscando quem vence em 7 dias (passando null para pegar todos os usuários)
        $assinaturasPertoDoVencimento = $assinaturaService->obterDiasAntesDoVencimento(7);

        if ($assinaturasPertoDoVencimento->isEmpty()) {
            logger()->info("Nenhuma assinatura para processar hoje.");
            return;
        }

        foreach ($assinaturasPertoDoVencimento as $assinatura) {
            logger()->info("Gerando fatura para: {$assinatura->user->name}");

            \DB::transaction(function () use ($faturaService, $assinatura, $mercadoPagoService) {
                $fatura = $faturaService->criarFatura([
                    'user_id' => $assinatura->user_id,
                    'assinatura_id' => $assinatura->id,
                    'valor' => $assinatura->plano->preco,
                    'status' => StatusPagamento::PENDENTE,
                    'vencimento_em' => $assinatura->data_proxima_cobranca,
                    'periodo_inicio' => $assinatura->data_proxima_cobranca,
                    'periodo_fim' => $assinatura->data_proxima_cobranca->copy()->addMonth(),
                    'url_pagamento' => null,
                    'referencia_externa' => null,
                    'metodo_pagamento' => null,
                    'tipo_cobranca' => TipoCobranca::CICLO_NORMAL
                ], $assinatura->user_id);

                $linkPagamento = $mercadoPagoService->criarLinkPagamento($fatura);

                $fatura->update([
                    'url_pagamento' => $linkPagamento->sandbox_init_point,
                    'referencia_externa' => $linkPagamento->id,
                ]);

                $assinatura->update([
                    'data_proxima_cobranca' => $assinatura->data_proxima_cobranca->addMonth()
                ]);

            });
        }

        logger()->info("Processamento finalizado com sucesso!");
    }
}
