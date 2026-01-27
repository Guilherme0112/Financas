<?php

namespace App\Http\Controllers;

use App\Enums\CategoriaEntrada;
use App\Enums\CategoriaSaida;
use App\Http\Requests\IndexLancamentosRequest;
use App\Http\Requests\StoreLancamentosRequest;
use App\Http\Requests\UpdateLancamentosRequest;
use App\Models\Categoria;
use App\Services\LancamentoService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LancamentoController extends Controller
{

    protected $lancamentoService;

    public function __construct(LancamentoService $lancamentoService)
    {
        $this->lancamentoService = $lancamentoService;
    }

    public function index(IndexLancamentosRequest $request): Response
    {
        $lancamentos = $this->lancamentoService->listar(
            $request->validated()
        );

        return Inertia::render('Lancamentos/Index', [
            'lancamentos' => $lancamentos,
            'filtros' => $request->only(['tipo', 'data_inicio', 'data_fim']),
            'categoriasEntrada' => array_map(fn($c) => [
                'value' => $c->value,
                'label' => $c->label(),
            ], CategoriaEntrada::cases()),
            'categoriasSaida' => array_map(fn($c) => [
                'value' => $c->value,
                'label' => $c->label(),
            ], CategoriaSaida::cases()),
        ]);
    }


    public function store(StoreLancamentosRequest $request, LancamentoService $service): RedirectResponse
    {
        $service->criar($request->validated());
        return redirect()->back()->with('success', 'Lançamento salvo com sucesso.');
    }

    public function update(UpdateLancamentosRequest $request, LancamentoService $service, $id): RedirectResponse
    {
        $service->atualizar($id, $request->validated());
        return redirect()->back()->with('success', 'Lançamento atualizado com sucesso.');
    }

    public function destroy($id, LancamentoService $service): void
    {
        $service->deletar($id);
    }

}
