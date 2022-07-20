<?php

declare(strict_types=1);

namespace App\Services\Bot\Settings\Contract;

use App\Services\Bot\Settings\Dto\Settings as SettingsDto;

interface SettingsServiceInterface
{
    public function parseSettings(array $botName): SettingsDto;
}
