<?php

use App\Http\Controllers\FaturasController;
use Illuminate\Support\Facades\Route;


Route::post('/webhook/mercado-pago/pagamento', [FaturasController::class, 'webhookMercadoPagoPagamento'])->name('webhookMercadoPagoPagamento');

Route::get('/ping', function () {
    return 'pong';
});