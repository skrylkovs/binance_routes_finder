<?php

declare(strict_types=1);

namespace App\Services\Exchange\CryptoExchanges;

use App\Services\Exchange\Contracts\CryptoExchangeInterface;
use App\Services\Exchange\Exceptions\RequestErrorException;
use ccxt\Exchange as CcxtBaseExchange;
use Psr\Log\LoggerInterface;
use Psy\Exception\FatalErrorException;
use Throwable;

class Binance extends BaseCryptoExchange implements CryptoExchangeInterface
{
    protected const BINANCE_API_EXCHANGE_INFO_METHOD = "exchangeInfo";
    protected const BINANCE_API_ORDERBOOK_METHOD = "depth";
    protected const BINANCE_API_OPEN_ORDERS_METHOD = "openOrders";
    protected const BINANCE_API_ALL_ORDERS_METHOD = "allOrders";
    protected const BINANCE_API_ACCOUNT_METHOD = "account";
    protected const BINANCE_API_CREATE_ORDER_METHOD = "order";

    protected string $name = "binance";

    /**
     * фиксированная комиссия только для тестового задания
     * в рабочей версии, я бы получал комиссию по API для конкретного юзера
     */
    protected float $tradeFee = 0.0001;

    public function __construct(
        protected LoggerInterface  $logger,
        protected CcxtBaseExchange $ccxtBinanceExchange,
    )
    {
    }

    public function getAvailablePairs(): array
    {
        $neededFields = array_flip(["baseAsset", "quoteAsset"]);
        $exchangeInfo = $this->fetch(self::BINANCE_API_EXCHANGE_INFO_METHOD);

        foreach ($exchangeInfo["symbols"] as $symbol) {
            if ($symbol["status"] === "TRADING") {
                $pairs[] = array_intersect_key($symbol, $neededFields);
            }
        }

        return $pairs;
    }

    public function getOrderBook(string $symbol, int $limit): array
    {
        return $this->fetch(self::BINANCE_API_ORDERBOOK_METHOD . "?symbol=" . $symbol . "&limit=" . $limit);
    }

    public function getUserOpenOrders(): array
    {
        return $this->fetch(self::BINANCE_API_OPEN_ORDERS_METHOD, "private");
    }

    public function getUserAllOrders(string $symbol): array
    {
        return $this->fetch(self::BINANCE_API_ALL_ORDERS_METHOD, "private", ["symbol" => $symbol]);
    }

    public function getUserAccount(): array
    {
        return $this->fetch(self::BINANCE_API_ACCOUNT_METHOD, "private");
    }

    public function createOrder(string $symbol, string $type, string $side, float $quantity, float $price, string $timeInForce): array
    {
        return $this->fetch(self::BINANCE_API_CREATE_ORDER_METHOD, "private", ["symbol" => $symbol, "type" => $type, "side" => $side, "quantity" => $quantity, "price" => $price, "timeInForce" => $timeInForce], "POST");
    }

    /**
     * @throws FatalErrorException
     */
    protected function fetch(string $path, string $apiType = 'public', array $params = [], string $method = "GET"): array
    {
        try {
            print_r($params);

            return $this->ccxtBinanceExchange->fetch2($path, $apiType, params: $params, method: $method);
        } catch (Throwable $exception) {
            $this->logger->error("Request error to exchange " . $this->getName(), [$exception, $path]);

            throw new RequestErrorException($exception->getMessage(), previous: $exception);
        }
    }
}
