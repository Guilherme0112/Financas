<?php

namespace App\Services;

use App\Models\Meta;
use App\Repositories\MetasRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class MetasService
{
    public function __construct(public MetasRepository $metasRepository)
    {
    }

    public function listar(array $filtros, int $userId, ?int $perPage = 20): LengthAwarePaginator
    {
        $paginator = $this->metasRepository->listar($filtros, $userId, $perPage);
        $paginator->getCollection()->transform(function ($meta) {
            $meta->soma_lancamentos = $meta->lancamentos->sum('valor');
            return $meta;
        });

        return $paginator;
    }


    public function criar(array $dados, int $userId): Meta
    {
        return $this->metasRepository->criar($dados, $userId);
    }

    public function atualizar(int $id, array $dados, int $userId): Meta
    {
        return $this->metasRepository->atualizar($id, $dados, $userId);
    }

    public function excluir(int $id, int $userId): void
    {
        $this->metasRepository->excluir($id, $userId);
    }
}

