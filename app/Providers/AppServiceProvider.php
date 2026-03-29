<?php

namespace App\Providers;

use App\Contracts\GatewayPagamentoInterface;
use App\Integrations\MercadoPagoService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            GatewayPagamentoInterface::class,
            MercadoPagoService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        if (request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        }
    }
}
