<?php

declare(strict_types=1);

namespace App\Services\User\Orders\Dto;

class Order
{
    public function __construct(
        protected int    $id,
        protected float  $price,
        protected float  $origQty,
        protected float  $executedQty,
        protected float  $avgPrice,
        protected string $status,
        protected string $type,
        protected string $side,
        protected int    $time,
        protected int    $updateTime,
    )
    {
    }
}
