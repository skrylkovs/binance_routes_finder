<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Exchange\Routes\Contracts\RouteFinderServiceInterface;
use Illuminate\Support\Facades\View;

class TradingBotController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        $api = new \Binance\API();

        exit;
    }
}
