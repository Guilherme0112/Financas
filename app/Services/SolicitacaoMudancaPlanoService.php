<?php

namespace App\Services;

use App\Models\SolicitacaoMudancaPlano;

class SolicitacaoMudancaPlanoService
{

    public function obterPorFaturaId(int $faturaId, int $userId)
    {
        return SolicitacaoMudancaPlano::query()
            ->where("user_id", $userId)
            ->where("fatura_id", $faturaId)
            ->latest();
    }

    public function criarSolicitacaoMudancaPlano(array $dados, int $userId)
    {
        return SolicitacaoMudancaPlano::create([
            'user_id' => $userId,
            'assinatura_id' => $dados["assinatura_id"],
            'fatura_id' => $dados["fatura_id"],
            'plano_antigo_id' => $dados["plano_antigo_id"],
            'plano_novo_id' => $dados["plano_novo_id"],
            'status' => $dados["status"],
        ]);
    }
}
