<?php

declare(strict_types=1);

namespace App\Services\Exchange\CryptoExchanges\Stream;

use Psr\Log\LoggerInterface;

class BinanceWebsocket
{
    protected const BINANCE_WS_HOST = "wss://stream.binance.com:9443/ws";
    protected const BINANCE_DEPTH_STREAM = "depth";

    protected string $name = "binance";

    public function __construct(
        protected LoggerInterface  $logger
    ) {
    }

    public function listenDepth(string $symbol, callable $callback): void
    {
        $reactConnector = new \React\Socket\Connector();
        $loop = \React\EventLoop\Loop::get();
        $connector = new \Ratchet\Client\Connector($loop, $reactConnector);

        $connector(self::BINANCE_WS_HOST . "/" . $symbol . "@" . self::BINANCE_DEPTH_STREAM)
            ->then(function(\Ratchet\Client\WebSocket $connection) use ($callback) {
                $connection->on('message', function(\Ratchet\RFC6455\Messaging\MessageInterface $msg) use ($connection, $callback) {
                    echo "Received: {$msg}\n";

                    call_user_func($callback);
                });

                $connection->on('close', function($code = null, $reason = null) {
                    echo "Connection closed ({$code} - {$reason})\n";
                });

            }, function(\Exception $e) use ($loop) {
                echo "Could not connect: {$e->getMessage()}\n";
                $loop->stop();
            });

        $loop->run();
    }
}
