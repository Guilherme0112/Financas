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
        return $this->metasRepository->listar($filtros, $userId, $perPage);
    }

    private function validarDados(array $dados, int $userId)
    {
        $dados['user_id'] = $userId;
        $dados["valor_objetivo"] = abs($dados["valor_objetivo"]);
        return $dados;
    }

    public function criar(array $dados, int $userId): Meta
    {
        $dados = $this->validarDados($dados, $userId);
        return $this->metasRepository->criar($dados);
    }

    public function atualizar(int $id, array $dados, int $userId): Meta
    {
        $dados = $this->validarDados($dados, $userId);
        return $this->metasRepository->atualizar($id, $dados);
    }

    public function excluir(int $id, int $userId): void
    {
        $this->metasRepository->excluir($id, $userId);
    }
}

