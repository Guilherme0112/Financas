<?php

namespace App\Contracts;

use App\Models\Fatura;

interface GatewayPagamentoInterface
{
    public function criarPagamento(Fatura $fatura): array;

    public function obterPagamentoPorId(string $pagamentoId): array;
}
