<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\arguments;

use CortexPE\Commando\args\StringEnumArgument;
use DaPigGuy\PiggyFactions\permissions\FactionPermission;
use DaPigGuy\PiggyFactions\permissions\PermissionFactory;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;

class PermissionEnumArgument extends StringEnumArgument
{
    public function parse(string $argument, CommandSender $sender): string
    {
        return $argument;
    }

    public function getTypeName(): string
    {
        return "permission";
    }

    public function getValue(string $string): ?Permission
    {
        return PermissionFactory::getPermission(strtolower($string));
    }

    public function getEnumValues(): array
    {
        return array_values(array_map(function (FactionPermission $permission): string {
            return $permission->getName();
        }, PermissionFactory::getPermissions()));
    }
}