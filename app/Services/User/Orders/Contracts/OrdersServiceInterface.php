<?php

declare(strict_types=1);

namespace App\Services\User\Orders\Contracts;

use App\Services\User\Orders\Dto\Orders;
interface OrdersServiceInterface
{
    public function parseUserOrders(array $orders): Orders;
}
