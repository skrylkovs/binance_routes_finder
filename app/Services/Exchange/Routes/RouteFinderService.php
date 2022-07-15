<?php

declare(strict_types=1);

namespace App\Services\Exchange\Routes;

use App\Enum\Exchange\OrderType;
use App\Services\Exchange\Contracts\OrdersServiceInterface;
use App\Services\Exchange\CryptoExchangesResolver;
use App\Services\Exchange\Exceptions\ExchangeException;
use App\Services\Exchange\Dto\{Trade, TradePair};
use App\Services\Exchange\Routes\Contracts\RouteFinderServiceInterface;
use App\Services\Exchange\Trading\Contracts\CalculateTradeServiceInterface;
use Psr\Log\LoggerInterface;

final class RouteFinderService implements RouteFinderServiceInterface
{
    public const ORDERBOOK_LIMIT = 500;

    protected string $subject;
    protected string $target;
    protected float $amount;
    protected array $suitedPairs;
    protected OrdersServiceInterface $cryptoExchange;

    public function __construct(
        protected CryptoExchangesResolver        $cryptoExchangesResolver,
        protected CalculateTradeServiceInterface $calculateTradeService,
        protected LoggerInterface                $logger
    ) {
        $this->cryptoExchange = $this->cryptoExchangesResolver->make(config("exchange.default"));
    }

    public function createRoutes(string $subject, string $target, float $amount): array
    {
        $this->subject = $subject;
        $this->target = $target;
        $this->amount = $amount;

        $this->fillSuitedPairs($this->cryptoExchange->getAvailablePairs());
        $this->filterSuitedPairs();
        $this->sortRoutes();
        $this->fillPairsQuantity();

        return $this->parseRoutes();
    }

    protected function fillSuitedPairs(array $pairs): void
    {
        $this->suitedPairs = [];

        foreach ($pairs as $pair) {
            if (in_array($this->subject, $pair) !== false) {
                $key = $this->subject === $pair['quoteAsset'] ? $pair['baseAsset'] : $pair['quoteAsset'];
            }

            if (in_array($this->target, $pair) !== false) {
                $key = $this->target === $pair['quoteAsset'] ? $pair['baseAsset'] : $pair['quoteAsset'];
            }

            if (isset($key)) {
                $pair['type'] = $this->getPairType($pair);
                $this->suitedPairs[$key][] = $pair;
            }

            unset($key);
        }
    }

    protected function getPairType($pair): OrderType
    {
        if (array_search($this->subject, $pair) === 'quoteAsset' || array_search($this->target, $pair) === 'baseAsset') {
            return OrderType::BIDS;
        }

        return OrderType::ASKS;
    }

    protected function filterSuitedPairs(): void
    {
        foreach ($this->suitedPairs as $key => $route) {
            if ($this->isDirectRoute($route) === false) {
                unset($this->suitedPairs[$key]);
            }
        }

        $this->suitedPairs = array_unique($this->suitedPairs, SORT_REGULAR);
    }

    protected function isDirectRoute(array $route): bool
    {
        $pair = $route[0];

        if (count($route) == 1 && in_array($this->subject, $pair) === false) {
            return false;
        }

        if (count($route) == 1 && in_array($this->target, $pair) === false) {
            return false;
        }

        return true;
    }

    protected function sortRoutes()
    {
        foreach ($this->suitedPairs as $currency => $pair) {
            if (count($pair) == 2 && in_array($this->subject, $pair[0]) === false) {
                $this->suitedPairs[$currency] = [$pair[1], $pair[0]];
            }
        }
    }

    protected function fillPairsQuantity()
    {
        foreach ($this->suitedPairs as $currency => $route) {
            try {
                foreach ($route as $key => $pair) {
                    $orderbook = $this->cryptoExchange->getOrderBook($pair['baseAsset'] . $pair['quoteAsset'], self::ORDERBOOK_LIMIT);
                    $orderbook = $this->cryptoExchange->parseOrderBook($orderbook, $pair['type']->getLowerCaseName());

                    $amount = $currentQuantity ?? $this->amount;

                    $this->suitedPairs[$currency][$key]['quantity'] = $this->amount;
                    $currentQuantity = $this->suitedPairs[$currency][$key]['resultQuantity'] = $this->calculateTradeService->calculateTargetQuantity($orderbook, $amount);
                }
                unset($currentQuantity);
            } catch (ExchangeException $exception) {
                $this->logger->error("Catched exchange exception", [$exception]);
                unset($this->suitedPairs[$currency]);

                continue;
            }
        }
    }

    protected function parseRoutes(): array
    {
        $routes = [];

        foreach ($this->suitedPairs as &$route) {
            $routeKey = $this->generateRouteKey($route);
            $this->logger->info("route {$routeKey} created", [$route]);

            foreach ($route as &$pair) {
                $tradePair = new TradePair($pair['baseAsset'], $pair['quoteAsset'], $pair['type']);
                $routes[$routeKey]["pairs"][] = new Trade($tradePair, $pair['quantity'], $pair['resultQuantity']);
            }
        }

        return $routes;
    }

    protected function generateRouteKey(array $route): string
    {
        $codes = [];
        foreach ($route as $pair) {
            $codes = $pair['type'] === OrderType::ASKS ? [...$codes, $pair['baseAsset'], $pair['quoteAsset']] : [... $codes, $pair['quoteAsset'], $pair['baseAsset']];
        }

        return implode("_", array_unique($codes));
    }

    public function parseTotalQuantities(array $routes): array
    {
        foreach ($routes as &$route) {
            $route = round(end($route["pairs"])->resultQuantity, 10);
        }

        arsort($routes);

        return $routes;
    }
}
