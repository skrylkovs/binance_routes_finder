<?php

declare(strict_types=1);

namespace App\Services\Exchange;

use App\Services\Exchange\Contracts\CryptoExchangeInterface;
use App\Services\Exchange\Exceptions\UnknownCryptoExchangeException;

class CryptoExchangesResolver
{
    /**
     * @var CryptoExchangeInterface[]
     */
    protected array $mapping = [];

    public function __construct(CryptoExchangeInterface ...$cryptoExchanges)
    {
        foreach ($cryptoExchanges as $cryptoExchange) {
            $this->mapping[$cryptoExchange->getName()] = $cryptoExchange;
        }
    }

    public function make(string $name): CryptoExchangeInterface
    {
        if (!isset($this->mapping[$name])) {
            throw new UnknownCryptoExchangeException("Unknown exchange $name");
        }

        return $this->mapping[$name];
    }
}
