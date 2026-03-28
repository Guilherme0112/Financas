<?php

namespace App\Repositories;

use App\Models\Plano;

class PlanoRepository
{
    public function obterPlanoPorNome(string $nome)
    {
        return Plano::where('plano', $nome)->firstOrFail();
    }
}
