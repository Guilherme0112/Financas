<?php

namespace App\Http\Controllers;

use App\Enums\CategoriaSaida;
use App\Http\Requests\IndexLimiteCategoriaRequest;
use App\Http\Requests\StoreLimiteCategoriaRequest;
use App\Http\Requests\UpdateLimiteCategoriaRequest;
use App\Services\LimiteCategoriaService;
use App\Services\MetasService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LimiteCategoriaController extends Controller
{
    public function index(IndexLimiteCategoriaRequest $request, LimiteCategoriaService $limiteCategoriaService, MetasService $metasService): Response
    {
        try {
            $data = $request->validated();
            $limites = $limiteCategoriaService->listar($data, auth()->id(), 6);
            $metas = $metasService->listar([], auth()->id());
            return Inertia::render('Metas/Index', [
                'categoriasSaida' => array_map(fn($c) => [
                    'value' => $c->value,
                    'label' => $c->label(),
                ], CategoriaSaida::cases()),
                "limites" => $limites,
                "metas" => $metas
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function store(StoreLimiteCategoriaRequest $request, LimiteCategoriaService $limiteCategoriaService): RedirectResponse
    {
        logger()->info("Atualizando meta de categoria", ['dados' => $request->all()]);
        try {
            $dados = $request->validated();
            $limiteCategoriaService->criar($dados, auth()->id());
            return redirect()->back()->with('success', 'Meta criada com sucesso!');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['erro' => $e->getMessage()]);
        }
    }

    public function update(int $id, UpdateLimiteCategoriaRequest $request, LimiteCategoriaService $limiteCategoriaService): RedirectResponse
    {
        logger()->info("Atualizando meta de categoria", ['id' => $id, 'dados' => $request->all()]);
        try {
            $dados = $request->validated();
            $limiteCategoriaService->atualizar($id, $dados, auth()->id());
            return redirect()->back()->with('success', 'Meta atualizada com sucesso!');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['erro' => $e->getMessage()]);
        }
    }

    public function destroy(int $id, LimiteCategoriaService $limiteCategoriaService): RedirectResponse
    {
        try {
            $limiteCategoriaService->excluir($id, auth()->id());
            return redirect()->back()->with('success', 'Meta excluída com sucesso!');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['erro' => $e->getMessage()]);
        }
    }
}
