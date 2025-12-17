<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GestaoController;
use App\Http\Controllers\ProfileController;
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

Route::prefix('gestao')->middleware('auth')->group(function () {
    Route::get('/', [GestaoController::class, 'index'])->name('gestao.index');
    Route::put('/{id}', [GestaoController::class, 'update'])->name('gestao.update');
    Route::post('/', [GestaoController::class, 'store'])->name('gestao.store');
    Route::delete('/{id}', [GestaoController::class, 'destroy'])->name('gestao.destroy');
});

Route::prefix('categorias')->middleware('auth')->group(function () {
    Route::get('/', [CategoriaController::class, 'index'])->name('categorias.index');
    Route::post('/', [CategoriaController::class, 'store'])->name('categorias.store');
});

require __DIR__.'/auth.php';
