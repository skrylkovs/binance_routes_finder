<?php

declare(strict_types=1);

namespace App\Services\User\Balance;

use App\Exceptions\InvalidResponseFormatException;
use App\Services\User\Balance\Contracts\BalanceServiceInterface;
use App\Services\User\Balance\Dto\Balance;

class BalanceService implements BalanceServiceInterface
{
    public function parseUserBalances(array $balances): array
    {
        if(!isset($balances["balances"]))
        {
            throw new InvalidResponseFormatException("balances hasn't found in array");
        }

        $parsedBalances = [];
        foreach ($balances["balances"] as &$balance) {
            $freeBalance = floatval($balance["free"]);

            if($freeBalance > 0)
            {
                $parsedBalances[$balance["asset"]] = new Balance($balance["asset"], $freeBalance);
            }
        }

        return $parsedBalances;
    }
}
