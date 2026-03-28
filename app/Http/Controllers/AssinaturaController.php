<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpgradePlanoRequest;
use App\Services\AssinaturaService;
use Inertia\Inertia;

class AssinaturaController extends Controller
{
    public function __construct(private AssinaturaService $assinaturaService) {}

    public function upgrade(StoreUpgradePlanoRequest $request)
    {
        $dados = $request->validated();
        $urlPagamento = $this->assinaturaService->prepararUpgrade($dados, auth()->user()->assinatura()->first(), auth()->id());
        return Inertia::location(($urlPagamento));
    }
}
