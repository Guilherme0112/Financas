<?php

use App\Http\Controllers\AssinaturaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FaturasController;
use App\Http\Controllers\LancamentoController;
use Illuminate\Http\Request;
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
})->name('welcome');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified', 'check.subscription'])->name('dashboard');

Route::middleware(['auth', 'check.subscription'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('lancamentos')
    ->middleware(['auth', 'check.subscription'])
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
    ->middleware(['auth', 'check.subscription'])
    ->name('limites.')
    ->group(function () {
        Route::get('/', action: [LimiteCategoriaController::class, 'index'])->name('index');
        Route::post('/', action: [LimiteCategoriaController::class, 'store'])->name('store');
        Route::put('/{id}', action: [LimiteCategoriaController::class, 'update'])->name('update');
        Route::delete('/{id}', action: [LimiteCategoriaController::class, 'destroy'])->name('destroy');
    });

Route::prefix('metas')
    ->middleware(['auth', 'check.subscription'])
    ->name('metas.')
    ->group(function () {
        Route::get('/', action: [MetasController::class, 'index'])->name('index');
        Route::post('/', action: [MetasController::class, 'store'])->name('store');
        Route::put('/{id}', action: [MetasController::class, 'update'])->name('update');
        Route::delete('/{id}', action: [MetasController::class, 'destroy'])->name('destroy');
    });

Route::prefix('importar')
    ->middleware(['auth', 'check.subscription'])
    ->name('importar.')
    ->group(function () {
        Route::post('/xlsx', [TrocaDeDadosController::class, 'importarXLSX'])->name('xlsx');
        Route::post('/csv', [TrocaDeDadosController::class, 'importarCSV'])->name('csv');
    });


Route::prefix(prefix: 'exportar')
    ->middleware(['auth', 'check.subscription'])
    ->name('exportar.')
    ->group(function () {
        Route::get("/download/{id}", [TrocaDeDadosController::class, 'download'])->name('download');
        Route::post('/xlsx', [TrocaDeDadosController::class, 'exportarXLSX'])->name('xlsx');
        Route::post('/pdf', [TrocaDeDadosController::class, 'exportarPDF'])->name('pdf');
    });

Route::prefix('faturas')
    ->middleware(['auth'])
    ->name('faturas.')
    ->group(function () {
        Route::get('/', [FaturasController::class, 'index'])->name('index');
    });

Route::prefix('prospeccao-futuro')
    ->middleware(['auth', 'check.subscription'])
    ->name('prospeccao-futuro.')
    ->group(function () {
        Route::get('/', [ProspeccaoFuturoController::class, 'index'])->name('index');
    });

Route::prefix("/assinatura")
    ->name('assinatura.')
    ->group(function () {
        Route::get('/expirada', function () {
            return Inertia::render('Outros/AssinaturaExpirada'); })->name('expirada');
        Route::get('/pendente', function () {
            return Inertia::render('Outros/AssinaturaPendente'); })->name('pendente');
        Route::post("/upgrade", [AssinaturaController::class, "upgrade"])->name("upgrade");
    });

Route::get('/pagamento/{status?}', function (Request $request) {
    $statusMP = $request->query('status') ?? $request->query('collection_status');
    $faturaId = $request->query('external_reference');

    $statusSugerido = match ($statusMP) {
        'approved' => 'success',
        'rejected', 'cancelled' => 'failed',
        'pending', 'in_process' => 'pending',
        default => 'pending'
    };

    return Inertia::render('Outros/PagamentoStatus', [
        'status' => $statusSugerido,
        'fatura_id' => $faturaId
    ]);
})->name('pagamento');

require __DIR__ . '/auth.php';
