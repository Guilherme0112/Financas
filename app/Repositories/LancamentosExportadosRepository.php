<?php

namespace App\Repositories;

use App\Models\LancamentosExportados;

class LancamentosExportadosRepository
{
    public function salvar(array $lancamentoExportados): LancamentosExportados
    {
        return LancamentosExportados::create($lancamentoExportados);
    }
}
