<?php

declare(strict_types=1);

namespace App\Enum\Exchange;

use App\Exceptions\UnknownOrderTypeException;

enum OrderType
{
    case ASKS;
    case BIDS;

    public static function fromColumn(string $column): self
    {
        $column = strtoupper($column);
        if(!defined("self::" . $column)){
            throw new UnknownOrderTypeException("Unknown orderbook column $column");
        }

        return constant("self::" . $column);
    }

    public function getLowerCaseName(): string
    {
        return strtolower($this->name);
    }

    public function mathOperator()
    {
        return match ($this) {
            self::ASKS => "*",
            self::BIDS => "/",
        };
    }
}
