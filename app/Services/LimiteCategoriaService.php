<?php

namespace App\Services;

use App\Models\LimiteCategoria;
use App\Repositories\LimiteCategoriaRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class LimiteCategoriaService
{

    public function __construct(public LimiteCategoriaRepository $limiteCategoriaRepository)
    {
    }

    public function listarMetas(array $filtros, int $userId): Collection
    {
        $limites = $this->limiteCategoriaRepository->listar($filtros, $userId);
        return $limites->map(function ($limite) {
            $limite->categoria_saida_label = $limite->categoria_saida->label();
            return $limite;
        });
    }

    public function listarMetasComLancamentos(int $userId): LengthAwarePaginator
    {
        $limites = $this->limiteCategoriaRepository->listarLimitesComLancamentos($userId);

        $limites->getCollection()->transform(function ($limite) {
            $limite->categoria_saida_label = $limite->categoria_saida->label();
            return $limite;
        });

        return $limites;

    }

    public function criar(array $dados, int $userId): LimiteCategoria
    {
        return $this->limiteCategoriaRepository->criar($dados, $userId);
    }

    public function atualizar(int $id, array $dados, int $userId): LimiteCategoria
    {
        return $this->limiteCategoriaRepository->atualizar($id, $dados, $userId);
    }

    public function excluir(int $id, int $userId): void
    {
        $this->limiteCategoriaRepository->excluir($id, $userId);
    }
}
