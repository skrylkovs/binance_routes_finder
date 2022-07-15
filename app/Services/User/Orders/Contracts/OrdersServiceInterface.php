<?php

declare(strict_types=1);

namespace App\Services\User\Orders\Contracts;

use ccxt\Exchange as CcxtBaseExchange;
use Psr\Log\LoggerInterface;

interface OrdersServiceInterface
{
    public function __construct(
        LoggerInterface  $logger,
        CcxtBaseExchange $ccxtBinanceExchange
    );

    public function fetchUserOrders(): array;

    public function getOrders();
}
