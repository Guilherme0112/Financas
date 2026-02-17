<?php

namespace App\Repositories;

use App\Enums\TipoValor;
use App\Models\Lancamento;
use App\Models\LimiteCategoria;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Collection;

class LimiteCategoriaRepository
{

    public function listar(array $filtros, int $userId): Collection
    {
        return LimiteCategoria::where('user_id', $userId)->get();
    }

    public function listarLimitesComLancamentos(int $userId, ?int $perPage = 20)
    {
        $limites = LimiteCategoria::where('user_id', $userId)
            ->paginate($perPage);

        $gastos = Lancamento::select(
            'categoria_saida',
            DB::raw("TO_CHAR(mes_referencia, 'YYYY-MM') as mes_referencia"),
            DB::raw('SUM(valor) as total_gasto')
        )
            ->where('user_id', $userId)
            ->where('tipo', TipoValor::SAIDA)
            ->whereFoiPago(true)
            ->groupBy('categoria_saida', DB::raw("TO_CHAR(mes_referencia, 'YYYY-MM')"))
            ->get()
            ->keyBy(
                fn($item) =>
                (is_object($item->categoria_saida) ? $item->categoria_saida->value : $item->categoria_saida)
                . '_' . $item->mes_referencia
            );

        $limites->getCollection()->transform(function ($limite) use ($gastos) {
            $categoria = is_object($limite->categoria_saida) ? $limite->categoria_saida->value : $limite->categoria_saida;
            $chave = $categoria . '_' . $limite->mes_referencia;
            $limite->total_gasto = $gastos[$chave]->total_gasto ?? 0;
            return $limite;
        });

        return $limites;
    }



    public function criar(array $dados, int $userId): LimiteCategoria
    {
        $dados['user_id'] = $userId;
        $quantidadeMeses = (int) ($dados['meses_recorrentes'] ?? 1);
        $dataBase = Carbon::parse($dados['mes_referencia']); // Carbon

        $limiteLancamentos = collect();

        DB::transaction(function () use ($dados, $quantidadeMeses, $dataBase, $userId, $limiteLancamentos) {
            for ($i = 0; $i < $quantidadeMeses; $i++) {
                $mesReferencia = $dataBase->copy()->addMonths($i)->format('Y-m');

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
        $limite = LimiteCategoria::where('id', $id)->where('user_id', $userId)->firstOrFail();
        $limite->update($dados);
        return $limite;
    }

    public function excluir(int $id, int $userId): void
    {
        $limite = LimiteCategoria::where('id', $id)->where('user_id', $userId)->firstOrFail();
        $limite->delete();
    }
}
