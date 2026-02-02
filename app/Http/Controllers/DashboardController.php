<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(DashboardService $dashboardService): Response
    {
        return Inertia::render('Dashboard/Index', [
            'dashboard' => $dashboardService->index(),
        ]);
    }
}
