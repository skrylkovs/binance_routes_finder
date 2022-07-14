<?php

namespace App\Services\Exchange\CryptoExchanges;

use App\Services\Exchange\Contracts\CryptoExchangeInterface;
use ccxt\Exchange as CcxtBaseExchange;
use Psr\Log\LoggerInterface;
use Psy\Exception\FatalErrorException;
use Throwable;

class Binance extends BaseCryptoExchange implements CryptoExchangeInterface
{
    protected const BINANCE_API_HOST = "https://api.binance.com";
    protected const BINANCE_API_EXCHANGE_INFO_URL = "/api/v3/exchangeInfo";
    protected const BINANCE_API_ORDERBOOK_URL = "/api/v3/depth";

    protected string $name = "binance";

    /**
     * фиксированная комиссия только для тестового задания
     * в рабочей версии, я бы получал комиссию по API для конкретного юзера
     */
    protected float $tradeFee = 0.0001;

    public function __construct(
        protected LoggerInterface  $logger,
        protected CcxtBaseExchange $ccxtBinanceExchange
    )
    {
    }

    public function getAvailablePairs(): array
    {
        $neededFields = array_flip(["baseAsset", "quoteAsset"]);
        $exchangeInfo = $this->fetch(self::BINANCE_API_HOST . self::BINANCE_API_EXCHANGE_INFO_URL);

        foreach ($exchangeInfo["symbols"] as $symbol) {
            if ($symbol["status"] === "TRADING") {
                $pairs[] = array_intersect_key($symbol, $neededFields);
            }
        }

        return $pairs;
    }

    public function getOrderBook(string $symbol, int $limit): array
    {
        return $this->fetch(self::BINANCE_API_HOST . self::BINANCE_API_ORDERBOOK_URL . "?symbol=" . $symbol . "&limit=" . $limit);
    }

    /**
     * @throws FatalErrorException
     */
    protected function fetch(string $url): array
    {
        try {
            return $this->ccxtBinanceExchange->fetch($url);
        } catch (Throwable $exception) {
            $this->logger->error("Request error to exchange " . $this->getName(), [$exception, $url]);

            throw new FatalErrorException($exception->getMessage(), previous: $exception);
        }
    }
}
