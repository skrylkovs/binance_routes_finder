<?php

declare(strict_types=1);

namespace App\Console\Commands\Bot;

use App\Services\Bot\Contracts\BotServiceInterface;
use App\Services\Exchange\Contracts\CryptoExchangeInterface;
use App\Services\Exchange\CryptoExchanges\Stream\BinanceWebsocket;
use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;

class AutoTradingBotCommand extends Command
{
    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint
     * @var string
     */
    protected $signature = 'gonzobot:auto-trading-bot';

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint
     * @var string
     */
    protected $description = '';

    public function __construct(
        protected BinanceWebsocket $binanceWebsocket,
        protected BotServiceInterface $botService,
        protected LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $this->botService->setSettings()->run();
        //$this->binanceWebsocket->listenDepth("xlmeth", function(){});
    }
}
