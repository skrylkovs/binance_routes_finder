<?php

namespace App\Services\Exchange\Contracts;

use App\Services\Exchange\Dto\Orderbook;
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
}
