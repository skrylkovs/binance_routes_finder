<?php

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
