<?php

declare(strict_types=1);

namespace App\Services\User\Balance\Contracts;

interface BalanceServiceInterface
{
    public function parseUserBalances(array $balances): array;
}
