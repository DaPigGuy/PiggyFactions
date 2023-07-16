<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\utils;

use pocketmine\utils\TextFormat;

class AsciiCompass
{
    const DIRECTIONS = [
        "N" => 'N',
        "NE" => '/',
        "E" => 'E',
        "SE" => '\\',
        "S" => 'S',
        "SW" => '/',
        "W" => 'W',
        "NW" => '\\',
        "NONE" => '+'
    ];

    const COLOR_ACTIVE = TextFormat::RED;
    const COLOR_INACTIVE = TextFormat::YELLOW;

    public static function getAsciiCompass(float $degrees): array
    {
        $rows = [
            ["NW", "N", "NE"],
            ["W", "NONE", "E"],
            ["SW", "S", "SE"]
        ];
        $direction = self::getDirectionsByDegrees($degrees);
        return array_map(function (array $directions) use ($direction): string {
            $row = "";
            foreach ($directions as $d) {
                $row .= ($direction === $d ? self::COLOR_ACTIVE : self::COLOR_INACTIVE) . self::DIRECTIONS[$d];
            }
            return $row;
        }, $rows);
    }

    public static function getDirectionsByDegrees(float $degrees): string
    {
        $degrees = ($degrees - 157.0) % 360.0;
        if ($degrees < 0) $degrees += 360.0;

        return array_keys(self::DIRECTIONS)[(int)floor($degrees / 45.0)];
    }
}