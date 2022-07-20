<?php

declare(strict_types=1);

namespace App\Services\Bot;

use App\Services\Bot\Contracts\BotServiceInterface;
use App\Services\Bot\Exceptions\IterationBreakException;
use App\Services\Bot\Settings\Contract\SettingsServiceInterface;
use App\Services\Exchange\CryptoExchangesResolver;
use App\Services\User\Balance\Contracts\BalanceServiceInterface;
use App\Services\User\Orders\Contracts\OrdersServiceInterface;
use App\Services\Bot\Settings\Dto\Settings as SettingsDto;
use App\Services\User\Orders\Dto\Orders;
use Psr\Log\LoggerInterface;

final class BotService implements BotServiceInterface
{
    protected int $iteration = 0;
    protected SettingsDto $settings;
    protected const ORDER_TYPE = "LIMIT";
    protected const TIME_IN_FORCE = "GTC";

    public function __construct(
        protected CryptoExchangesResolver  $cryptoExchangesResolver,
        protected OrdersServiceInterface   $ordersService,
        protected BalanceServiceInterface  $balanceService,
        protected SettingsServiceInterface $settingsService,
        protected LoggerInterface          $logger
    )
    {
        $this->cryptoExchange = $this->cryptoExchangesResolver->make(config("exchange.default"));
    }

    public function setSettings(string $name = "skrylkovs1")
    {
        $this->settings = $this->settingsService->parseSettings(config("bots.$name"));

        return $this;
    }

    public function run(): void
    {
        $this->iterate();
    }

    protected function iterate(): bool
    {
        $openedOrders = $this->ordersService->parseUserOrders(
            $this->cryptoExchange->getUserOpenOrders()
        );

        print_r($openedOrders);

        $allOrders = $this->ordersService->parseUserOrders(
            $this->cryptoExchange->getUserAllOrders($this->settings->symbol)
        );

        try {
            $this->sellOrdersProcess($openedOrders->sell, $openedOrders->totalCount, $openedOrders->sellOrderMinutesAgo);
            $this->historyOrdersProcess($allOrders->sell, $allOrders->totalCount, $allOrders->sellOrderMinutesAgo);
        } catch (IterationBreakException $exception) {
            return true;//$this->iterate();
        }

        return true;



        $balances = $this->balanceService->parseUserBalances(
            $this->cryptoExchange->getUserAccount()
        );

        print_r($balances);

        // $this->iterate();

        return true;
    }

    protected function sellOrdersProcess(array $orders, int $totalOpenedCount, int $sellOrderMinutesAgo): void
    {
        if ($this->isItTimeToCreateDeal($totalOpenedCount, $sellOrderMinutesAgo) === true) {
            $price = 1000;
            $quantity = round(10 / $price, 8);

            //$this->cryptoExchange->createOrder($this->settings->symbol, self::ORDER_TYPE, "BUY", $quantity, $price, self::TIME_IN_FORCE);

            throw new IterationBreakException("Sell order has created, break iteration");
        }
    }

    protected function historyOrdersProcess(array $orders, int $totalOpenedCount, int $sellOrderMinutesAgo): void
    {
        if ($this->isItTimeToCreateDeal($totalOpenedCount, $sellOrderMinutesAgo) === true) {
            $price = 1000;
            $quantity = round(10 / $price, 8);

            $this->cryptoExchange->createOrder($this->settings->symbol, self::ORDER_TYPE, "SELL", $quantity, $price, self::TIME_IN_FORCE);

            throw new IterationBreakException("Sell order has created, break iteration");
        }
    }

    protected function isItTimeToCreateDeal(int $totalOpenedCount, int $sellOrderMinutesAgo): bool
    {
        echo "$totalOpenedCount === 0 || $sellOrderMinutesAgo >= {$this->settings->periodInMinutes}";

        return $totalOpenedCount === 0 || $sellOrderMinutesAgo >= $this->settings->periodInMinutes;
    }
}
