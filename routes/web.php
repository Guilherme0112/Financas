<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LancamentoController;
use App\Http\Controllers\LimiteCategoriaController;
use App\Http\Controllers\MetasController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProspeccaoFuturoController;
use App\Http\Controllers\TrocaDeDadosController;
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
        Route::put('/{id}/marcar-como-paga', [LancamentoController::class, 'marcarComoPaga'])->name('marcar-como-paga');
        Route::post('/', [LancamentoController::class, 'store'])->name('store');
        Route::delete('/{id}', [LancamentoController::class, 'destroy'])->name('destroy');
        Route::post('/bulk/delete', [LancamentoController::class, 'destroyBulk'])->name('destroy-bulk');
    });

Route::prefix('limites')
    ->middleware('auth')
    ->name('limites.')
    ->group(function () {
        Route::get('/', action: [LimiteCategoriaController::class, 'index'])->name('index');
        Route::post('/', action: [LimiteCategoriaController::class, 'store'])->name('store');
        Route::put('/{id}', action: [LimiteCategoriaController::class, 'update'])->name('update');
        Route::delete('/{id}', action: [LimiteCategoriaController::class, 'destroy'])->name('destroy');
    });

Route::prefix('metas')
    ->middleware('auth')
    ->name('metas.')
    ->group(function () {
        Route::get('/', action: [MetasController::class, 'index'])->name('index');
        Route::post('/', action: [MetasController::class, 'store'])->name('store');
        Route::put('/{id}', action: [MetasController::class, 'update'])->name('update');
        Route::delete('/{id}', action: [MetasController::class, 'destroy'])->name('destroy');
    });

Route::prefix('importar')
    ->middleware('auth')
    ->name('importar.')
    ->group(function () {
        Route::post('/xlsx', [TrocaDeDadosController::class, 'importarXLSX'])->name('xlsx');
        Route::post('/csv', [TrocaDeDadosController::class, 'importarCSV'])->name('csv');
    });


Route::prefix(prefix: 'exportar')
    ->middleware('auth')
    ->name('exportar.')
    ->group(function () {
        Route::get("/download/{id}", [TrocaDeDadosController::class, 'download'])->name('download');
        Route::post('/xlsx', [TrocaDeDadosController::class, 'exportarXLSX'])->name('xlsx');
        Route::post('/pdf', [TrocaDeDadosController::class, 'exportarPDF'])->name('pdf');
    });

Route::prefix('prospeccao-futuro')
    ->middleware('auth')
    ->name('prospeccao-futuro.')
    ->group(function () {
        Route::get('/', [ProspeccaoFuturoController::class, 'index'])->name('index');
    });

require __DIR__ . '/auth.php';
