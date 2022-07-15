<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Exchange\Contracts\CryptoExchangeInterface;
use App\Services\Exchange\CryptoExchanges\Binance;
use App\Services\Exchange\CryptoExchangesResolver;
use App\Services\Exchange\Routes\Contracts\RouteFinderServiceInterface;
use App\Services\Exchange\Routes\RouteFinderService;
use App\Services\Exchange\Trading\CalculateTradeService;
use App\Services\Exchange\Trading\Contracts\CalculateTradeServiceInterface;
use ccxt\binance as CcxtBinanceExchange;
use ccxt\Exchange as CcxtBaseExchange;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(RouteFinderServiceInterface::class, RouteFinderService::class);
        $this->app->bind(CryptoExchangeInterface::class, Binance::class);
        $this->app->bind(CcxtBaseExchange::class, CcxtBinanceExchange::class);
        $this->app->bind(CalculateTradeServiceInterface::class, CalculateTradeService::class);

        $this->app
            ->when(CryptoExchangesResolver::class)
            ->needs(CryptoExchangeInterface::class)
            ->give(function ($app) {
                return [
                    $app->make(Binance::class),
                ];
            });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
