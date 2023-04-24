<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\arguments;

use DaPigGuy\PiggyFactions\libs\CortexPE\Commando\args\StringEnumArgument;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use pocketmine\command\CommandSender;

class LanguageEnumArgument extends StringEnumArgument
{
    public function parse(string $argument, CommandSender $sender): string
    {
        return $argument;
    }

    public function getTypeName(): string
    {
        return "language";
    }

    public function getValue(string $string): string
    {
        return $string;
    }

    public function getEnumValues(): array
    {
        return LanguageManager::LANGUAGES;
    }

    public function getEnumName(): string
    {
        return "language";
    }
}