<?php

declare(strict_types=1);

namespace App\Services\Bot\Settings\Dto;

class Settings
{
    public function __construct(
        public readonly string $symbol,
        public readonly string $currencyFiat,
        public readonly string $currencyCrypto,
        public readonly int $quantityFiat,
        public readonly int $periodInMinutes,
    )
    {
    }
}
