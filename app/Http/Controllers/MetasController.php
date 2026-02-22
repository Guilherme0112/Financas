<?php

namespace App\Http\Controllers;

use App\Enums\CategoriaSaida;
use App\Http\Requests\IndexMetaRequest;
use App\Http\Requests\StoreLimiteCategoriaRequest;
use App\Http\Requests\StoreMetaRequest;
use App\Http\Requests\UpdateLimiteCategoriaRequest;
use App\Http\Requests\UpdateMetaRequest;
use App\Services\MetasService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MetasController extends Controller
{
    public function index(IndexMetaRequest $request, MetasService $metaService): Response
    {
        $data = $request->validated();
        $limites = $metaService->listar($data, auth()->id());
        return Inertia::render('Metas/Index', [
            'categoriasSaida' => array_map(fn($c) => [
                'value' => $c->value,
                'label' => $c->label(),
            ], CategoriaSaida::cases()),
            "metas" => $limites
        ]);
    }

    public function store(StoreMetaRequest $request, MetasService $metaService): RedirectResponse
    {
        logger()->info("Criando nova meta", ['dados' => $request->all()]);
        try {
            $dados = $request->validated();
            $metaService->criar($dados, auth()->id());
            return redirect()->back()->with('success', 'Meta criada com sucesso!');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['erro' => $e->getMessage()]);
        }
    }

    public function update(int $id, UpdateMetaRequest $request, MetasService $metaService): RedirectResponse
    {
        logger()->info("Atualizando meta", ['id' => $id, 'dados' => $request->all()]);
        try {
            $dados = $request->validated();
            $metaService->atualizar($id, $dados, auth()->id());
            return redirect()->back()->with('success', 'Meta atualizada com sucesso!');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['erro' => $e->getMessage()]);
        }
    }

    public function destroy(int $id, MetasService $metaService): RedirectResponse
    {
        try {
            $metaService->excluir($id, auth()->id());
            return redirect()->back()->with('success', 'Meta excluída com sucesso!');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['erro' => $e->getMessage()]);
        }
    }
}

