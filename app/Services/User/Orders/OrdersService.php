<?php

declare(strict_types=1);

namespace App\Services\User\Orders;

use App\Services\User\Orders\Contracts\OrdersServiceInterface;
use ccxt\Exchange as CcxtBaseExchange;
use Psr\Log\LoggerInterface;

class OrdersService implements OrdersServiceInterface
{
    public function __construct(
        LoggerInterface  $logger,
        CcxtBaseExchange $ccxtBinanceExchange
    ){

    }

    public function fetchUserOrders(): array
    {

    }

    public function getOrders(){

    }
}
