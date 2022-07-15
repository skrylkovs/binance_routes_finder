<?php

declare(strict_types=1);

namespace App\Services\Exchange\Dto;

class OrderbookRow
{
    public function __construct(
        public readonly float $price,
        public readonly float $amount,
    ) {
    }
}
