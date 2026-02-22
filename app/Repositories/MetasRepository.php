<?php

namespace App\Repositories;

use App\Models\Meta;
use Arr;
use Illuminate\Pagination\LengthAwarePaginator;

class MetasRepository
{
    public function listar(array $filtros, int $userId, ?int $perPage = 20): LengthAwarePaginator
    {
        return Meta::query()
            ->with('lancamentos')
            ->where("user_id", $userId)
            ->when(Arr::get($filtros, "search_metas"), function ($q, $search) {
                $q->where("nome", "like", "%{$search}%");
            })
            ->orderBy('nome', 'asc')
            ->paginate(1);
    }

    public function listarPaginado(int $userId, ?int $perPage = 20): LengthAwarePaginator
    {
        return Meta::where('user_id', $userId)
            ->with('lancamentos')
            ->paginate($perPage);
    }

    public function obterPorId(int $id, int $userId): Meta
    {
        return Meta::where('id', $id)->where('user_id', $userId)->firstOrFail();
    }

    public function criar(array $dados, int $userId): Meta
    {
        $dados['user_id'] = $userId;
        return Meta::create($dados);
    }

    public function atualizar(int $id, array $dados, int $userId): Meta
    {
        $meta = Meta::where('id', $id)->where('user_id', $userId)->firstOrFail();
        $meta->update($dados);
        return $meta;
    }

    public function excluir(int $id, int $userId): void
    {
        $meta = Meta::where('id', $id)->where('user_id', $userId)->firstOrFail();
        $meta->delete();
    }
}

