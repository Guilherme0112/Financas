<?php

namespace App\Http\Controllers;

use App\Services\LancamentoService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(LancamentoService $lancamentoService): Response
    {
        return Inertia::render('Dashboard', [
            'dashboard' => $lancamentoService->dashboard(),
        ]);
    }
}
