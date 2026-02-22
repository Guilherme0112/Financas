<?php

namespace App\Http\Controllers;

use App\Enums\CategoriaEntrada;
use App\Enums\CategoriaSaida;
use App\Enums\TipoValor;
use App\Http\Requests\IndexLancamentosRequest;
use App\Http\Requests\StoreLancamentosRequest;
use App\Http\Requests\UpdateLancamentosRequest;
use App\Services\LancamentoService;
use App\Services\MetasService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LancamentoController extends Controller
{
    public function __construct(public LancamentoService $lancamentoService, public MetasService $metasService)
    {
    }

    public function index(IndexLancamentosRequest $request): Response
    {
        $data = $request->validated();
        $lancamentos = $this->lancamentoService->listar((array) $data, null, auth()->id());
        $metas = $this->metasService->listar($data, auth()->id());
        return Inertia::render('Lancamentos/Index', [
            'lancamentos' => $lancamentos,
            "metas" => $metas,
            'categoriasEntrada' => array_map(fn($c) => [
                'value' => $c->value,
                'label' => $c->label(),
                ], CategoriaEntrada::cases()),
                'categoriasSaida' => array_map(fn($c) => [
                    'value' => $c->value,
                'label' => $c->label(),
                ], CategoriaSaida::cases()),
            'tipo' => array_map(fn($c) => [
                'value' => $c->value,
                'label' => $c->label(),
                ], TipoValor::cases())
        ]);
    }


    public function store(StoreLancamentosRequest $request, LancamentoService $lancamentoService): RedirectResponse
    {
        try {
            $dados = $request->validated();
            $lancamentoService->criar($dados);
            return redirect()->back()->with('success', 'Lançamento salvo com sucesso.');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors('erro', $e->getMessage());
        }
    }

    public function update(UpdateLancamentosRequest $request, LancamentoService $lancamentoService, $id): RedirectResponse
    {
        try {
            $dados = $request->validated();
            $lancamentoService->atualizar($id, $dados);
            return redirect()->back()->with('success', 'Lançamento atualizado com sucesso.');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors('erro', $e->getMessage());
        }
    }

    public function destroy($id, LancamentoService $lancamentoService): void
    {
        $lancamentoService->deletar($id);
    }
}
