<?php

namespace App\Http\Controllers;

use App\Services\LancamentoService;
use App\Services\MetasService;
use Inertia\Inertia;
use Inertia\Response;

class ProspeccaoFuturoController extends Controller
{
    public function index(MetasService $metasService, LancamentoService $lancamentoService): Response
    {
        $metas = $metasService->listar([], auth()->id(), 6);
        $media = $lancamentoService->obterTotaisMensaisPorPeriodo(now()->subMonths(3), now(), auth()->id());
        return Inertia::render("ProspeccaoFuturo/Index", [
            "metas" => $metas,
            "media" => $media
        ]);
    }
}
