<?php

declare(strict_types=1);

namespace App\Services\Exchange\Routes\Contracts;

interface RouteFinderServiceInterface
{
    public function createRoutes(string $subject, string $target, float $amount);

    public function parseTotalQuantities(array $routes): array;
}
