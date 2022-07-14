<?php

namespace App\Services\Exchange\CryptoExchanges;

use App\Enum\Exchange\OrderType;
use App\Services\Exchange\Dto\{Orderbook, OrderbookRow};

abstract class BaseCryptoExchange
{
    public function getName(): string
    {
        return $this->name;
    }

    public function getTradeFee(): float
    {
        return $this->tradeFee;
    }

    public function parseOrderBook(array $orderbook, string $column): Orderbook
    {
        foreach ($orderbook[$column] as $row) {
            $orders[] = new OrderbookRow($row[0], $row[1]);
        }

        return new Orderbook($orders, OrderType::fromColumn($column));
    }
}
