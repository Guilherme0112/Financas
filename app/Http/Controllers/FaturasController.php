<?php

namespace App\Http\Controllers;

use App\Http\Requests\WebhookMercadoPagoPagamentoRequest;
use App\Services\FaturaService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FaturasController extends Controller
{

    public function __construct(private FaturaService $faturaService)
    {}

    public function index()
    {
        return Inertia::render("Faturas/Index", [
            'faturas' => $this->faturaService->obterFaturas(auth()->id())
        ]);
    }

    public function webhookMercadoPagoPagamento(WebhookMercadoPagoPagamentoRequest $request)
    {
        $dados = $request->validated();
        $this->faturaService->webhookMercadoPagoPagamento($dados);
    }
}
