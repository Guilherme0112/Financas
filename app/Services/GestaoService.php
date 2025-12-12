<?php

namespace App\Services;

use App\Models\Lancamento;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GestaoService
{
    public function listar(int $perPage = 10): LengthAwarePaginator
    {
        return Lancamento::query()
            ->latest()
            ->paginate($perPage);
    }

    public function criar(array $dados): Lancamento
    {
        return Lancamento::create($dados);
    }

    public function atualizar(string $id, array $dados): Lancamento
    {
        $lancamento = Lancamento::findOrFail($id);
        $lancamento->update($dados);

        return $lancamento;
    }

    public function deletar(string $id): void
    {
        $lancamento = Lancamento::findOrFail($id);
        $lancamento->delete();
    }
}
