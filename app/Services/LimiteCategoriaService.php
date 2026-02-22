<?php

namespace App\Services;

use App\Models\LimiteCategoria;
use App\Repositories\LimiteCategoriaRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class LimiteCategoriaService
{

    public function __construct(public LimiteCategoriaRepository $limiteCategoriaRepository)
    {
    }

    public function listar(array $filtros, int $userId, ?int $perPage = 20): LengthAwarePaginator
    {
        $limites = $this->limiteCategoriaRepository->listar($filtros, $userId, $perPage);

        $limites->getCollection()->transform(function ($limite) {
            $limite->categoria_saida_label = $limite->categoria_saida->label();
            return $limite;
        });

        return $limites;
    }

    public function listarMetasComLancamentos(array $filtros, int $userId, ?int $perPage = 20): LengthAwarePaginator
    {
        $limites = $this->limiteCategoriaRepository->listar($filtros, $userId, $perPage);

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
