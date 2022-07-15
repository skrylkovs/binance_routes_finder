<?php

declare(strict_types=1);

namespace App\Services\Exchange\Dto;

use App\Enum\Exchange\OrderType;

class Orderbook
{
    public function __construct(
        public readonly array     $orders,
        public readonly OrderType $type,
    ) {
    }
}
