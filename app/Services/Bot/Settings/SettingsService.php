<?php

declare(strict_types=1);

namespace App\Services\Bot\Settings;

use App\Services\Bot\Settings\Dto\Settings as SettingsDto;
use App\Services\Bot\Settings\Contract\SettingsServiceInterface;

final class SettingsService implements SettingsServiceInterface
{
    public function parseSettings(array $settings): SettingsDto
    {
        $settings["symbol"] = $this->getSymbol($settings);

        return new SettingsDto(...$settings);
    }

    protected function getSymbol(array $settings): string
    {
        return $settings["currencyCrypto"] . $settings["currencyFiat"];
    }
}
