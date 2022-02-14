<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\permissions;

use DaPigGuy\PiggyFactions\utils\Relations;
use DaPigGuy\PiggyFactions\utils\Roles;

class PermissionFactory
{
    /** @var FactionPermission[] */
    public static array $permissions;

    public static function init(): void
    {
        self::registerPermission(new FactionPermission(FactionPermission::ALLY, [Roles::LEADER]));
        self::registerPermission(new FactionPermission(FactionPermission::BAN, [Roles::LEADER, Roles::OFFICER]));
        self::registerPermission(new FactionPermission(FactionPermission::BUILD, [Roles::LEADER, Roles::OFFICER, Roles::MEMBER]));
        self::registerPermission(new FactionPermission(FactionPermission::CLAIM, [Roles::LEADER, Roles::OFFICER]));
        self::registerPermission(new FactionPermission(FactionPermission::CONTAINERS, [Roles::LEADER, Roles::OFFICER, Roles::MEMBER]));
        self::registerPermission(new FactionPermission(FactionPermission::DEMOTE, [Roles::LEADER]));
        self::registerPermission(new FactionPermission(FactionPermission::DESCRIPTION, [Roles::LEADER, Roles::OFFICER]));
        self::registerPermission(new FactionPermission(FactionPermission::ENEMY, [Roles::LEADER]));
        self::registerPermission(new FactionPermission(FactionPermission::FLAG, [Roles::LEADER]));
        self::registerPermission(new FactionPermission(FactionPermission::FLY, [Roles::LEADER, Roles::OFFICER, Roles::MEMBER, Roles::RECRUIT, Relations::ALLY]));
        self::registerPermission(new FactionPermission(FactionPermission::INVITE, [Roles::LEADER, Roles::OFFICER]));
        self::registerPermission(new FactionPermission(FactionPermission::INTERACT, [Roles::LEADER, Roles::OFFICER, Roles::MEMBER, Roles::RECRUIT]));
        self::registerPermission(new FactionPermission(FactionPermission::KICK, [Roles::LEADER, Roles::OFFICER]));
        self::registerPermission(new FactionPermission(FactionPermission::MOTD, [Roles::LEADER, Roles::OFFICER]));
        self::registerPermission(new FactionPermission(FactionPermission::NAME, [Roles::LEADER]));
        self::registerPermission(new FactionPermission(FactionPermission::NEUTRAL, [Roles::LEADER]));
        self::registerPermission(new FactionPermission(FactionPermission::PROMOTE, [Roles::LEADER]));
        self::registerPermission(new FactionPermission(FactionPermission::SETHOME, [Roles::LEADER, Roles::OFFICER]));
        self::registerPermission(new FactionPermission(FactionPermission::UNALLY, [Roles::LEADER]));
        self::registerPermission(new FactionPermission(FactionPermission::UNBAN, [Roles::LEADER, Roles::OFFICER]));
        self::registerPermission(new FactionPermission(FactionPermission::UNCLAIM, [Roles::LEADER, Roles::OFFICER]));
    }

    /**
     * @return FactionPermission[]
     */
    public static function getPermissions(): array
    {
        return self::$permissions;
    }

    public static function getPermission(string $name): ?FactionPermission
    {
        return (self::$permissions[$name] ?? null) === null ? null : clone self::$permissions[$name];
    }

    public static function registerPermission(FactionPermission $permission): void
    {
        self::$permissions[$permission->getName()] = $permission;
    }
}