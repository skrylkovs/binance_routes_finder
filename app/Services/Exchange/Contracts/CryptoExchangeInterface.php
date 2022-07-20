<?php

declare(strict_types=1);

namespace App\Services\Exchange\Contracts;

use App\Services\Exchange\Dto\Orderbook;
use App\Services\User\Orders\Dto\Orders;
use ccxt\Exchange as CcxtBaseExchange;
use Psr\Log\LoggerInterface;

interface CryptoExchangeInterface
{
    public function __construct(
        LoggerInterface  $logger,
        CcxtBaseExchange $ccxtBinanceExchange
    );

    public function getName(): string;

    public function getTradeFee(): float;

    public function getAvailablePairs(): array;

    public function getOrderBook(string $symbol, int $limit): array;

    public function parseOrderBook(array $orderbook, string $column): Orderbook;

    public function getUserOpenOrders(): array;

    public function getUserAllOrders(string $symbol): array;

    public function getUserAccount(): array;
}
