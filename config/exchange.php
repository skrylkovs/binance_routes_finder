<?php

return [
    'default' => 'binance',
    'exchanges' =>
    [
        'binance' => [
            'order_map' => [
                'id' => 'orderId',
                'price' => 'price',
                'origQty' => 'orderId',
                'executedQty' => 'executedQty',
                'avgPrice' => 'price',
                'status' => 'status',
                'type' => 'type',
                'side' => 'side',
                'time' => 'time',
                'updateTime' => 'updateTime'
            ]
        ]
    ]
];
