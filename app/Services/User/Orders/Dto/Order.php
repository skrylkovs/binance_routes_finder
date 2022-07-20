<?php

declare(strict_types=1);

namespace App\Services\User\Orders\Dto;

class Order
{
    public function __construct(
        public readonly int    $id,
        public readonly float  $price,
        public readonly float  $origQty,
        public readonly float  $executedQty,
        public readonly float  $avgPrice,
        public readonly string $status,
        public readonly string $type,
        public readonly string $side,
        public readonly int    $time,
        public readonly int    $updateTime,
    )
    {
    }
}
