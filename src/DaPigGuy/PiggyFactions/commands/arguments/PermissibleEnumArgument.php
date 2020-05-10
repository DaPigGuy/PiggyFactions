<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\arguments;

use CortexPE\Commando\args\StringEnumArgument;
use DaPigGuy\PiggyFactions\utils\Relations;
use DaPigGuy\PiggyFactions\utils\Roles;
use pocketmine\command\CommandSender;

class PermissibleEnumArgument extends StringEnumArgument
{
    const VALUES = [
        "leader" => Roles::LEADER,
        "officer" => Roles::OFFICER,
        "member" => Roles::MEMBER,
        "recruit" => Roles::RECRUIT,
        "ally" => Relations::ALLY,
        "truced" => Relations::TRUCE,
        "neutral" => Relations::NONE,
        "enemy" => Relations::ENEMY
    ];

    public function parse(string $argument, CommandSender $sender): string
    {
        return $argument;
    }

    public function getTypeName(): string
    {
        return "permissible";
    }
}