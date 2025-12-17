<?php

namespace App\Http\Controllers;

use App\Services\GestaoService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(GestaoService $gestaoService)
    {
        return Inertia::render('Dashboard', [
            'dashboard' => $gestaoService->dashboard(),
        ]);
    }
}
