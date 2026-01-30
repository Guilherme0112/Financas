<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LancamentoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SimuladorController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('lancamentos')
    ->middleware('auth')
    ->name('lancamentos.')
    ->group(function () {
    Route::get('/', action: [LancamentoController::class, 'index'])->name('index');
    Route::put('/{id}', [LancamentoController::class, 'update'])->name('update');
    Route::post('/', [LancamentoController::class, 'store'])->name('store');
    Route::delete('/{id}', [LancamentoController::class, 'destroy'])->name('destroy');
});

require __DIR__.'/auth.php';
