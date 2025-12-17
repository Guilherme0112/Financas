<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLancamentosRequest;
use App\Http\Requests\UpdateLancamentosRequest;
use App\Models\Categoria;
use App\Services\GestaoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GestaoController extends Controller
{

    protected $gestaoService;

    public function __construct(GestaoService $gestaoService)
    {
        $this->gestaoService = $gestaoService;
    }

    public function index(Request $request): Response
    {
        return Inertia::render('Gestao/Index', [
            'lancamentos' => $this->gestaoService->listar(10),
            'categorias' => Categoria::orderBy('nome')->get(),
        ]);
    }

    public function store(StoreLancamentosRequest $request, GestaoService $service): RedirectResponse
    {
        $service->criar($request->validated());
        return redirect()->back()->with('success', 'Lançamento salvo com sucesso.');
    }

    public function update(UpdateLancamentosRequest $request, GestaoService $service, $id): RedirectResponse
    {
        $service->atualizar($id, $request->validated());
        return redirect()->back()->with('success', 'Lançamento atualizado com sucesso.');
    }

    public function destroy($id, GestaoService $service): void
    {
        $service->deletar($id);
    }

}
