<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\arguments;

use CortexPE\Commando\args\StringEnumArgument;
use DaPigGuy\PiggyFactions\flags\Flag;
use DaPigGuy\PiggyFactions\flags\FlagFactory;
use pocketmine\command\CommandSender;

class FlagEnumArgument extends StringEnumArgument
{
    public function parse(string $argument, CommandSender $sender): string
    {
        return $argument;
    }

    public function getTypeName(): string
    {
        return "flag";
    }

    public function getValue(string $string): ?Flag
    {
        return FlagFactory::getFlag(strtolower($string));
    }

    public function getEnumValues(): array
    {
        return array_values(array_map(function (Flag $flag): string {
            return $flag->getName();
        }, FlagFactory::getFlags()));
    }

    public function getEnumName(): string
    {
        return "flag";
    }
}