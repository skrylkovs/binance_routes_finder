<?php

declare(strict_types=1);

namespace App\Services\Exchange\Trading\Contracts;

use App\Services\Exchange\Dto\Orderbook;

interface CalculateTradeServiceInterface
{
    public function calculateTargetQuantity(Orderbook $orderbook, float $neededQuantity): float;
}
