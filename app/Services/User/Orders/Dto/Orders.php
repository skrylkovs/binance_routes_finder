<?php

declare(strict_types=1);

namespace App\Services\User\Orders\Dto;

class Orders
{
    public function __construct(
        public readonly array $buy,
        public readonly array $sell,
        public readonly int $buyCount,
        public readonly int $sellCount,
        public readonly int $totalCount,
        public readonly int $sellOrderTime,
        public readonly int $sellOrderMinutesAgo,
    )
    {
    }
}
