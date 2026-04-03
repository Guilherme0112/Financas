<?php

namespace App\Services;

use App\Contracts\GatewayPagamentoInterface;
use App\Enums\MetodoPagamento;
use App\Enums\Planos;
use App\Enums\StatusAssinatura;
use App\Enums\StatusPagamento;
use App\Enums\StatusSolicitacaoMudancaPlano;
use App\Enums\TipoCobranca;
use App\Models\Assinatura;
use App\Models\Fatura;
use App\Models\Plano;
use App\Models\User;
use Carbon\Carbon;

class FaturaService
{

    public function __construct(
        private GatewayPagamentoInterface $gatewayPagamento
    ) {
    }

    public function obterFaturas(string $userId, int $perPage = 20)
    {
        return Fatura::query()
            ->where("user_id", $userId)
            ->latest()
            ->paginate($perPage);
    }

    public function obterFaturaPorId(string $id)
    {
        return Fatura::findOrFail($id);
    }

    public function obterFaturasPorStatus(StatusPagamento $statusPagamento, string $userId, int $perPage = 20)
    {
        return Fatura::query()
            ->where("user_id", $userId)
            ->where("status", $statusPagamento)
            ->latest()
            ->paginate($perPage);
    }

    public function criarFatura(array $fatura, int $userId): Fatura
    {
        return Fatura::create([
            'user_id' => $userId ?? null,
            'assinatura_id' => $fatura["assinatura_id"] ?? null,
            'valor' => $fatura["valor"] ?? null,
            'status' => $fatura["status"] ?? null,
            'metodo_pagamento' => $fatura["metodo_pagamento"] ?? null,
            'vencimento_em' => $fatura["vencimento_em"] ?? null,
            'periodo_inicio' => $fatura["periodo_inicio"] ?? null,
            'periodo_fim' => $fatura["periodo_fim"] ?? null,
            'referencia_externa' => $fatura["referencia_externa"] ?? null,
            'url_pagamento' => $fatura["url_pagamento"] ?? null,
            'pago_em' => $fatura["pago_em"] ?? null,
            'tipo_cobranca' => $fatura["tipo_cobranca"] ?? null
        ]);
    }

    // TODO: criar método para trocar código repetido
    public function criarFaturaComLinkDePagamento()
    {

    }

    public function criarFaturaAssinatura(Assinatura $assinatura)
    {
        $plano = $assinatura->plano;
        $proximaData = $assinatura->data_fim ?? Carbon::now();
        $fatura = $this->criarFatura([
            'user_id' => $assinatura->user_id,
            'plano_id' => $assinatura->plano_id,
            'valor' => $plano->valor,
            'data_vencimento' => $proximaData->addMonth(),
            'status' => StatusPagamento::PENDENTE,
            'metodo_pagamento' => $assinatura->metodo_pagamento,
        ], $assinatura->user_id);

        $assinatura->update([
            'data_fim' => $fatura->data_vencimento,
        ]);

        return $fatura;
    }

    public function webhookMercadoPagoPagamento(array $dados)
    {
        logger()->info("Dados recebidos do webhook", $dados);
        $preferencia = $this->gatewayPagamento->obterPagamentoPorId($dados["data"]["id"]);
        $idExternoPagamento = $preferencia["external_reference"];

        logger()->info("Id externo/fatura é: " . $idExternoPagamento);

        $fatura = $this->obterFaturaPorId($idExternoPagamento);
        logger()->info("Fatura encontrada com sucesso", $fatura->toArray());

        if ($preferencia["status"] === 'approved') {
            logger()->info("Pagamento aprovado com sucesso");
            $this->processarPagamentoAprovado($fatura, $preferencia);
        }
    }

    public function processarPagamentoAprovado(Fatura $fatura, array $preferencia)
    {
        \DB::transaction(function () use ($fatura, $preferencia) {
            $metodoEnum = MetodoPagamento::deMercadoPago(
                $preferencia["payment_type_id"],
                $preferencia["payment_method_id"]
            );

            if($fatura->status === StatusPagamento::APROVADO) {
                logger()->warning("Fatura {$fatura->id} já está aprovada. Ignorando processamento.");
                return;
            }

            $fatura->update([
                "pago_em" => now(),
                "status" => StatusPagamento::APROVADO,
                "metodo_pagamento" => $metodoEnum
            ]);

            $assinatura = $fatura->assinatura;
            $novoPlanoId = $assinatura->plano_id;
            if ($fatura->tipo_cobranca === TipoCobranca::UPGRADE) {
                $solicitacao = $fatura->solicitacoesMudancaPlanos()
                    ->where('status', StatusSolicitacaoMudancaPlano::PENDENTE)
                    ->first();

                if ($solicitacao) {
                    $novoPlanoId = $solicitacao->plano_novo_id;
                    $solicitacao->update(['status' => StatusSolicitacaoMudancaPlano::CONCLUIDO]);
                    logger()->info("Upgrade detectado. Trocando plano {$solicitacao->plano_antigo_id} para {$novoPlanoId}");
                }
            }

            $assinatura->update([
                "plano_id" => $novoPlanoId,
                "status" => StatusAssinatura::ATIVA,
                "data_proxima_cobranca" => $assinatura->calcularProximoVencimento(),
                "data_inicio" => $assinatura->data_inicio ?? now(),
                "data_fim" => $assinatura->calcularProximoVencimento(),
            ]);

            $fatura->user->update(['is_active' => true]);
            logger()->info("Fluxo de pagamento concluído para usuário {$fatura->user->id}");
        });
    }
    public function processarFluxoFinanceiro(User $user, Assinatura $assinatura, Plano $plano): array
    {
        if ($plano->plano === Planos::GRATUITO) {
            logger()->info("Usuário redirecionado para o dashboard (Plano Gratuito)");
            return [
                'user' => $user,
                'redirect' => route('dashboard'),
                'external' => false
            ];
        }

        $fatura = $this->criarFatura([
            'user_id' => $user->id,
            'assinatura_id' => $assinatura->id,
            'valor' => $plano->preco,
            'status' => StatusPagamento::PENDENTE,
            'vencimento_em' => null,
            'periodo_inicio' => now(),
            'periodo_fim' => now(),
            "tipo_cobranca" => TipoCobranca::CICLO_NORMAL
        ], $user->id);
        logger()->info("Fatura inicial criada com sucesso", $fatura->toArray());

        $linkPagamento = $this->gatewayPagamento->criarPagamento($fatura);
        logger()->info("Link de pagamento gerado com sucesso");

        $fatura->update([
            'url_pagamento' => $linkPagamento["sandbox_init_point"],
            'referencia_externa' => $linkPagamento["id"],
        ]);
        logger()->info("Fatura atualizada com link de pagamento e referencia externa");

        return [
            'user' => $user,
            'redirect' => $linkPagamento["sandbox_init_point"],
            'external' => true
        ];
    }
}
