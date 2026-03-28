<?php

namespace App\Services;

use App\Enums\Planos;
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
        private MercadoPagoService $mercadoPagoService,
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
        $isGratuito = $plano->plano === Planos::GRATUITO;
        return $this->criar([
            'plano_id' => $plano->id,
            'data_inicio' => $isGratuito ? now() : null,
            'data_fim' => $isGratuito ? now()->addDays(7) : null,
            'data_proxima_cobranca' => $isGratuito ? now()->addDays(7) : null,
            'status' => $isGratuito ? StatusAssinatura::ATIVA : StatusAssinatura::PENDENTE,
        ], $user->id);
    }

    public function prepararUpgrade(array $dados, Assinatura $assinatura, int $userId): string
    {
        $planoNovoId = $dados["plano_id"];
        $planoNovo = $this->planoService->obterPlanoPorId($planoNovoId);
        return $this->fazerUpgrade($assinatura, $planoNovo);
    }

    public function fazerUpgrade(Assinatura $assinatura, Plano $novoPlano)
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

            $linkPagamento = $this->mercadoPagoService->criarLinkPagamento($fatura);

            $fatura->update([
                'url_pagamento' => $linkPagamento->sandbox_init_point,
                'referencia_externa' => $linkPagamento->id,
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
                    $hoje = now();
                    $vencimentoAtual = $assinatura->data_proxima_cobranca ?? $hoje;
                    $dataBase = $vencimentoAtual->gt($hoje) ? $vencimentoAtual : $hoje;
                    $novoVencimento = $dataBase->copy()->addMonth();

                    $assinatura->update([
                        'plano_id' => $solicitacao->plano_novo_id,
                        'status' => StatusAssinatura::ATIVA,
                        'data_proxima_cobranca' => $novoVencimento,
                        'data_fim' => $novoVencimento,
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

    public function criar(array $dados, int $userId): Assinatura
    {
        $dados['user_id'] = $userId;
        return Assinatura::query()->create($dados);
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
