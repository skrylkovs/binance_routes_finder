<?php

declare(strict_types=1);

namespace App\Services\Exchange\Dto;

use App\Enum\Exchange\OrderType;

class TradePair
{
    public function __construct(
        public readonly string     $baseCurrency,
        public readonly string     $quoteCurrency,
        public readonly OrderType  $type,
    ) {
    }
}
