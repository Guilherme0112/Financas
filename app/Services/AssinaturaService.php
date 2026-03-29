<?php

namespace App\Services;

use App\Contracts\GatewayPagamentoInterface;
use App\Enums\StatusAssinatura;
use App\Enums\StatusPagamento;
use App\Enums\StatusSolicitacaoMudancaPlano;
use App\Enums\TipoCobranca;
use App\Models\Assinatura;
use App\Models\Fatura;
use App\Models\Plano;
use App\Models\User;
use DB;

class AssinaturaService
{

    public function __construct(
        private GatewayPagamentoInterface $gatewayPagamento,
        private FaturaService $faturaService,
        private SolicitacaoMudancaPlanoService $solicitacaoMudancaPlanoService,
        private PlanoService $planoService
    ) {
    }

    public function listar(int $userId, ?int $perPage = 20)
    {
        $query = Assinatura::query()->where('user_id', $userId)->with('plano');
        if ($perPage) {
            return $query->paginate($perPage);
        }
        return $query->get();
    }

    public function prepararAssinaturaInicial(User $user, Plano $plano): Assinatura
    {
        return $this->criar(Assinatura::configurarDatasIniciais($plano), $user->id);
    }

    public function prepararUpgrade(array $dados, Assinatura $assinatura, int $userId): string
    {
        $planoNovoId = $dados["plano_id"];
        $planoNovo = $this->planoService->obterPlanoPorId($planoNovoId);
        return $this->fazerUpgrade($assinatura, $planoNovo);
    }


    public function fazerUpgrade(Assinatura $assinatura, Plano $novoPlano): string
    {
        return DB::transaction(function () use ($assinatura, $novoPlano) {
            $fatura = $this->faturaService->criarFatura([
                'user_id' => $assinatura->user_id,
                'assinatura_id' => $assinatura->id,
                'valor' => $novoPlano->preco,
                'status' => StatusPagamento::PENDENTE,
                'vencimento_em' => null,
                'periodo_inicio' => null,
                'periodo_fim' => null,
                'tipo_cobranca' => TipoCobranca::UPGRADE
            ], $assinatura->user_id);
            logger()->info("Fatura de upgrade criada com sucesso");

            $linkPagamento = $this->gatewayPagamento->criarPagamento($fatura);
            $fatura->update([
                'url_pagamento' => $linkPagamento["sandbox_init_point"],
                'referencia_externa' => $linkPagamento["id"],
            ]);

            $this->solicitacaoMudancaPlanoService->criarSolicitacaoMudancaPlano([
                'assinatura_id' => $assinatura->id,
                'fatura_id' => $fatura->id,
                'plano_antigo_id' => $assinatura->plano->id,
                'plano_novo_id' => $novoPlano->id,
                'status' => StatusSolicitacaoMudancaPlano::PENDENTE,
            ], $assinatura->user_id);
            logger()->info("Solicitação de upgrade criada com sucesso");

            return $fatura->url_pagamento;
        });
    }

    public function confirmarUpgrade(Fatura $fatura)
    {
        return DB::transaction(function () use ($fatura) {
            $fatura->update([
                'status' => StatusPagamento::APROVADO,
                'pago_em' => now(),
            ]);
            logger()->info("Fatura paga e atualizada com sucesso");

            if ($fatura->tipo_cobranca === TipoCobranca::UPGRADE) {

                $solicitacao = $fatura->solicitacoesMudancaPlanos()
                    ->where('status', StatusSolicitacaoMudancaPlano::PENDENTE)
                    ->first();

                if ($solicitacao) {
                    logger()->info("Solicitação de Upgrade encontrada");
                    $assinatura = $fatura->assinatura;
                    $assinatura->update([
                        'plano_id' => $solicitacao->plano_novo_id,
                        'status' => StatusAssinatura::ATIVA,
                        'data_proxima_cobranca' => $assinatura->calcularProximoVencimento(),
                        'data_fim' => $assinatura->calcularProximoVencimento(),
                    ]);
                    logger()->info("Assinatura atualizada com novo plano");

                    $solicitacao->update(['status' => StatusSolicitacaoMudancaPlano::CONCLUIDO]);
                    logger()->info("Solicitação concluída com sucesso");
                }
            }
        });
    }

    public function obterDiasAntesDoVencimento(int $diasParaVencer = 7)
    {
        $dataAlvo = now()->addDays($diasParaVencer)->toDateString();
        $query = Assinatura::query()
            ->with(['plano', 'user'])
            ->whereDate('data_proxima_cobranca', $dataAlvo)
            ->where('status', 'ATIVA');
        return $query->get();
    }

    public function obterPorId(int $id, int $userId): Assinatura
    {
        return Assinatura::query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();
    }

    public function criar(Assinatura $assinatura, int $userId): Assinatura
    {
        $assinatura->user_id = $userId;
        return Assinatura::query()->create($assinatura->toArray());
    }


    public function atualizar(int $id, array $dados, int $userId): Assinatura
    {
        $assinatura = $this->obterPorId($id, $userId);
        $assinatura->update($dados);
        return $assinatura;
    }


    public function excluir(int $id, int $userId): void
    {
        $assinatura = $this->obterPorId($id, $userId);
        $assinatura->delete();
    }
}
