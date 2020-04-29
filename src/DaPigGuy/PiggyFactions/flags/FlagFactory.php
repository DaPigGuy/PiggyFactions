<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\flags;

class FlagFactory
{
    /** @var Flag[] */
    public static $flags;

    public static function init(): void
    {
        self::registerPermission(new Flag(Flag::OPEN, true, false));
        self::registerPermission(new Flag(Flag::WARZONE, false, false));
        self::registerPermission(new Flag(Flag::SAFEZONE, false, false));
    }

    /**
     * @return Flag[]
     */
    public static function getFlags(): array
    {
        return self::$flags;
    }

    public static function getFlag(string $name): ?Flag
    {
        return (self::$flags[$name] ?? null) === null ? null : clone self::$flags[$name];
    }

    public static function registerPermission(Flag $flag): void
    {
        self::$flags[$flag->getName()] = $flag;
    }
}