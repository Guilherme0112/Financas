<?php

namespace App\Repositories;

use App\Enums\StatusPagamento;
use App\Models\Fatura;

class FaturaRepository
{
    public function obterFaturasPendentesPorUserId(string $userId, int $perPage)
    {
        return Fatura::query()
            ->where("user_id", $userId)
            ->where("status", StatusPagamento::PENDENTE)
            ->latest()
            ->paginate($perPage);
    }
}
