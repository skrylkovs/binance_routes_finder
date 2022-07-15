<?php

declare(strict_types=1);

namespace App\Services\Exchange\Dto;

class Trade
{
    public function __construct(
        public readonly TradePair  $tradePair,
        public readonly float      $quantity,
        public readonly float      $resultQuantity,
    ) {
    }
}
