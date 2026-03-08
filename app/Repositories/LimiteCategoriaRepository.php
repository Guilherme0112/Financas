<?php

namespace App\Repositories;

use App\Models\LimiteCategoria;
use Arr;
use Carbon\Carbon;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;

class LimiteCategoriaRepository
{

    public function listar(array $filtros, int $userId, ?int $perPage = 20): LengthAwarePaginator
    {
        $ano = Arr::get($filtros, 'ano');
        $mes = Arr::get($filtros, 'mes');

        return LimiteCategoria::query()
            ->where('user_id', $userId)
            ->when(!$ano || !$mes, function ($q) {
                $q->whereYear('mes_referencia', now()->year)
                    ->whereMonth('mes_referencia', now()->month);
            })
            ->when($ano && $mes, function ($q) use ($ano, $mes) {
                $q->whereYear('mes_referencia', $ano)
                    ->whereMonth('mes_referencia', $mes);
            })
            ->paginate($perPage)
            ->withQueryString();
    }

    public function obterPorUserIdAndId(int $id, int $userId): LimiteCategoria
    {
        return LimiteCategoria::where('id', $id)->where('user_id', $userId)->firstOrFail();
    }

    public function criar(array $dados, int $userId): LimiteCategoria
    {
        $dados['user_id'] = $userId;
        $quantidadeMeses = (int) ($dados['meses_recorrentes'] ?? 1);
        $dataBase = Carbon::parse($dados['mes_referencia']);

        $limiteLancamentos = collect();

        DB::transaction(function () use ($dados, $quantidadeMeses, $dataBase, $userId, $limiteLancamentos) {
            for ($i = 0; $i < $quantidadeMeses; $i++) {
                $mesReferencia = $dataBase->copy()->addMonths($i)->format('Y-m-d');

                $dados['mes_referencia'] = $mesReferencia;
                $dados['user_id'] = $userId;

                $limiteLancamentos->push(
                    LimiteCategoria::create($dados)
                );
            }
        });

        return $limiteLancamentos->first();
    }


    public function atualizar(int $id, array $dados, int $userId): LimiteCategoria
    {
        $limite = $this->obterPorUserIdAndId($id, $userId);
        $limite->update($dados);
        return $limite;
    }

    public function excluir(int $id, int $userId): void
    {
        $limite = $this->obterPorUserIdAndId($id, $userId);
        $limite->delete();
    }
}
