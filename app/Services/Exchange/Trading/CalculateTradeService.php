<?php

declare(strict_types=1);

namespace App\Services\Exchange\Trading;

use App\Services\Exchange\Contracts\OrdersServiceInterface;
use App\Services\Exchange\Trading\Contracts\CalculateTradeServiceInterface;
use App\Services\Exchange\Dto\Orderbook;
use App\Services\Exchange\Exceptions\OrdersIsNotEnoughException;

class CalculateTradeService implements CalculateTradeServiceInterface
{
    public function __construct(
        protected OrdersServiceInterface $cryptoExchange,
    ) {
    }

    public function calculateTargetQuantity(Orderbook $orderbook, float $neededQuantity): float
    {
        $orders = $orderbook->orders;
        $type = $orderbook->type;

        if ($this->isOrdersEnough($orders, $neededQuantity) === false) {
            throw new OrdersIsNotEnoughException("Trading amount is not enough for deal");
        }

        $mathOperator = $type->mathOperator();

        $targetQuantity = 0;
        $i = 0;

        while ($neededQuantity > 0) {
            $order = $orders[$i];

            if (!isset($orders[$i])) {
                throw new OrdersIsNotEnoughException("Trading amount is not enough for deal");
            }

            $orderAmount = $order->amount;
            $orderPrice = $order->price;

            if ($orderAmount > $neededQuantity) {
                $orderAmount = $neededQuantity;
            }

            $neededQuantity -= $orderAmount;
            $targetQuantity += $this->calculateQuantity($orderAmount, $orderPrice, $mathOperator);

            $i++;
        }

        return $targetQuantity - $targetQuantity * $this->cryptoExchange->getTradeFee();
    }

    private function calculateQuantity(float $orderAmount, float $orderPrice, string $operator): float
    {
        return eval("return $orderAmount $operator $orderPrice;");
    }

    private function calculateOrderbookQuantity(array $orders): float
    {
        $quantity = array_reduce($orders, function ($temp, $order) {
            return $temp + $order->amount;
        });

        return $quantity;
    }

    private function isOrdersEnough(array $orders, float $neededQuantity): bool
    {
        if ($neededQuantity > $this->calculateOrderbookQuantity($orders)) {
            return false;
        }

        return true;
    }
}
