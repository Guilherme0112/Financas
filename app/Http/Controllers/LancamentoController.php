<?php

namespace App\Http\Controllers;

use App\Enums\CategoriaEntrada;
use App\Enums\CategoriaSaida;
use App\Enums\TipoValor;
use App\Http\Requests\DestroyLancamentosRequest;
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
    public function __construct(public LancamentoService $lancamentoService, public MetasService $metasService) {}

    public function index(IndexLancamentosRequest $request): Response
    {
        $dados = $request->validated();
        $lancamentosResultado = $this->lancamentoService->listar((array) $dados, 25, auth()->id());
        $metas = $this->metasService->listar($dados, auth()->id());

        return Inertia::render('Lancamentos/Index', [
            'lancamentos' => $lancamentosResultado['paginacao'],
            'resumo' => $lancamentosResultado['resumo'],
            'metas' => $metas,
            'categoriasEntrada' => CategoriaEntrada::options(),
            'categoriasSaida' => CategoriaSaida::options(),
            'tipo' => TipoValor::options(),
        ]);
    }

    public function indexByKanbam(IndexLancamentosRequest $request)
    {
        $dados = $request->validated();
        $dadosKanban = $this->lancamentoService->listarPorKanbam($dados, 10, auth()->id());
        return Inertia::render('Lancamentos/Index', [
            'kanban' => $dadosKanban['kanban'],
            'resumo' => $dadosKanban['resumo'],
            'filtros' => $dados,
        ]);
    }

    public function store(StoreLancamentosRequest $request, LancamentoService $lancamentoService): RedirectResponse
    {
        try {
            $dados = $request->validated();
            $lancamentoService->criar(auth()->id(), $dados);

            return redirect()->back()->with('success', 'Lançamento salvo com sucesso.');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors('erro', $e->getMessage());
        }
    }

    public function marcarComoPaga($id, LancamentoService $lancamentoService): RedirectResponse
    {
        try {
            $lancamentoService->marcarComoPaga($id, auth()->id());

            return redirect()->back()->with('success', 'Lançamento marcado como pago com sucesso.');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors('erro', $e->getMessage());
        }
    }

    public function update(UpdateLancamentosRequest $request, LancamentoService $lancamentoService, $id): RedirectResponse
    {
        try {
            $dados = $request->validated();
            $lancamentoService->atualizar($id, auth()->id(), $dados);

            return redirect()->back()->with('success', 'Lançamento atualizado com sucesso.');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors('erro', $e->getMessage());
        }
    }

    public function destroy($id, LancamentoService $lancamentoService): void
    {
        $lancamentoService->deletar($id, auth()->id());
    }

    public function destroyBulk(DestroyLancamentosRequest $request, LancamentoService $lancamentoService): RedirectResponse
    {
        $dados = $request->validated();
        try {
            $lancamentoService->deletarVarios($dados['ids'], auth()->id());

            return redirect()->back()->with('success', count($dados['ids']).' lançamento(s) deletado(s) com sucesso.');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors('erro', $e->getMessage());
        }
    }
}
