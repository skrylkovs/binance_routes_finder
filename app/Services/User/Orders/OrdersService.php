<?php

declare(strict_types=1);

namespace App\Services\User\Orders;

use App\Services\User\Orders\Contracts\OrdersServiceInterface;
use App\Services\User\Orders\Dto\{Order, Orders};
use \Skrylkovs\Library\Casting;

class OrdersService implements OrdersServiceInterface
{
    public function __construct()
    {
        $exchange = config("exchange.default");
        $this->orderMap = config("exchange.exchanges.$exchange.order_map");
    }

    public function parseOrder(array $order): Order
    {
        foreach ($this->orderMap as $left => $right)
        {
            $params[$left] = Casting::byValueFormat($order[$right]);
        }

        return new Order(...$params);
    }

    public function parseUserOrders(array $orders): Orders
    {
        $dividedOrders = [
            "BUY" => [],
            "SELL" => [],
        ];

        foreach ($orders as $order) {
            $order = $this->parseOrder($order);
            $dividedOrders[$order->side][] = $order;
        }

        $buyCount = count($dividedOrders["BUY"]);
        $sellCount = count($dividedOrders["SELL"]);
        $totalCount = $buyCount + $sellCount;

        if($sellCount > 0)
        {
            $sellOrderTime = intval(end($dividedOrders["SELL"])->time / 1000);
            $sellOrderMinutesAgo = time() - $sellOrderTime;
        }

        return new Orders($dividedOrders["BUY"], $dividedOrders["SELL"], $buyCount, $sellCount, $totalCount, $sellOrderTime ?? 0, $sellOrderMinutesAgo ?? 0);
    }
}
