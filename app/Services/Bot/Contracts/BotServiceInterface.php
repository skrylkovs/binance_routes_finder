<?php

declare(strict_types=1);

namespace App\Services\Bot\Contracts;

use App\Services\Bot\Settings\Contract\SettingsServiceInterface;
use App\Services\Exchange\CryptoExchangesResolver;
use App\Services\User\Balance\Contracts\BalanceServiceInterface;
use App\Services\User\Orders\Contracts\OrdersServiceInterface;
use Psr\Log\LoggerInterface;

interface BotServiceInterface
{
    public function __construct(
        CryptoExchangesResolver $cryptoExchangesResolver,
        OrdersServiceInterface $ordersService,
        BalanceServiceInterface $balanceService,
        SettingsServiceInterface $settingsService,
        LoggerInterface $logger
    );

    public function run(): void;
}
