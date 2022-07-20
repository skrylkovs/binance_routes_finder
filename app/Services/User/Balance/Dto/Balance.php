<?php

declare(strict_types=1);

namespace App\Services\User\Balance\Dto;

class Balance
{
    public function __construct(
        public readonly string $currency,
        public readonly float  $quantity,
    )
    {
    }
}
