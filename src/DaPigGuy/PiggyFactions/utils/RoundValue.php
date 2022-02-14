<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\utils;

class RoundValue
{
    public static function round(float $value): float {
        return round($value, 2, PHP_ROUND_HALF_DOWN);
    }

    public static function roundToString(float $value): string {
        return (string)self::round($value);
    }
}