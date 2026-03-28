<?php

namespace App\Services;

use App\Enums\Planos;
use App\Models\Plano;
use App\Repositories\PlanoRepository;

class PlanoService
{

    public function __construct(private PlanoRepository $planoRepository)
    {
    }

    public function obterPlanoPorNome(string $nome): Plano
    {
        $nomePlano = Planos::tryFrom($nome);
        return $this->planoRepository->obterPlanoPorNome($nomePlano->value);
    }

    public function obterPlanoPorId(int $planoId)
    {
        return Plano::findOrFail($planoId);
    }
}
