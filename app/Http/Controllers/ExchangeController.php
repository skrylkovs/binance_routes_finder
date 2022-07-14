<?php

namespace App\Http\Controllers;

use App\Services\Exchange\Routes\Contracts\RouteFinderServiceInterface;
use Illuminate\Support\Facades\View;

class ExchangeController extends Controller
{
    public function __construct(protected RouteFinderServiceInterface $routeFinderService)
    {
    }

    public function index()
    {
        $data = [
            [
                "subject" => "ETH",
                "target" => "XEM",
                "quantity" => 0.009
            ],
            [
                "subject" => "BTC",
                "target" => "USDT",
                "quantity" => 0.56
            ],
            /*
            [
                "subject" => "XLM",
                "target" => "ETH",
                "quantity" => 10
            ],
            [
                "subject" => "XRP",
                "target" => "XLM",
                "quantity" => 15
            ],
            */
        ];

        foreach ($data as $deal){
            $key = $deal["subject"] . "-" .  $deal["target"];
            $routes = $this->routeFinderService->createRoutes($deal["subject"], $deal["target"], $deal["quantity"]);
            $results[$key] = $this->routeFinderService->parseTotalQuantities($routes);
        }

        print_r($results);exit;
        return View::make('exchange', ['results' => $results]);
    }
}
