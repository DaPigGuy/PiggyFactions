<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\utils;

use DaPigGuy\PiggyFactions\claims\ClaimsManager;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\format\Chunk;

class AsciiMap
{
    const MAP_KEY_CHARS = "\\/#?ç¬£$%=&^ABCDEFGHJKLMNOPQRSTUVWXYZÄÖÜÆØÅ1234567890abcdeghjmnopqrsuvwxyÿzäöüæøåâêîûô";

    const MAP_WIDTH = 48;
    const MAP_HEIGHT = 10;

    const MAP_KEY_MIDDLE = TextFormat::AQUA . "+";
    const MAP_KEY_WILDERNESS = TextFormat::GRAY . "-";
    const MAP_KEY_OVERFLOW = TextFormat::WHITE . "-" . TextFormat::RESET;
    const MAP_OVERFLOW_MESSAGE = self::MAP_KEY_OVERFLOW . ": Too Many Factions (>86) on this Map.";

    public static function getMap(Player $player, int $width, int $height): array
    {
        $centerX = $player->getPosition()->getFloorX() >> Chunk::COORD_BIT_SIZE;
        $centerZ = $player->getPosition()->getFloorZ() >> Chunk::COORD_BIT_SIZE;
        $centerFaction = ($claim = ClaimsManager::getInstance()->getClaimByPosition($player->getPosition())) === null ? null : $claim->getFaction();

        $compass = AsciiCompass::getAsciiCompass($player->getLocation()->yaw);

        $map = [LanguageManager::getInstance()->getMessage(LanguageManager::getInstance()->getPlayerLanguage($player), "commands.map.header", ["{X}" => $centerX, "{Z}" => $centerZ, "{FACTION}" => $centerFaction === null ? "Wilderness" : $centerFaction->getName()])];

        $legend = [];
        $characterIndex = 0;
        $overflown = false;

        for ($dz = 0; $dz < $height; $dz++) {
            $row = "";
            for ($dx = 0; $dx < $width; $dx++) {
                $chunkX = $centerX - ($width / 2) + $dx;
                $chunkZ = $centerZ - ($height / 2) + $dz;
                if ($chunkX === $centerX && $chunkZ === $centerZ) {
                    $row .= self::MAP_KEY_MIDDLE;
                    continue;
                }

                $faction = ($claim = ClaimsManager::getInstance()->getClaim((int)$chunkX, (int)$chunkZ, $player->getWorld()->getFolderName())) === null ? null : $claim->getFaction();

                if ($faction === null) {
                    $row .= self::MAP_KEY_WILDERNESS;
                } elseif (($symbol = array_search($faction, $legend)) === false && $overflown) {
                    $row .= self::MAP_KEY_OVERFLOW;
                } else {
                    if ($symbol === false) $legend[($symbol = self::MAP_KEY_CHARS[$characterIndex++])] = $faction;
                    if ($characterIndex === strlen(self::MAP_KEY_CHARS)) $overflown = true;
                    $row .= LanguageManager::getInstance()->getColorFor($player, $faction) . $symbol;
                }
            }

            if ($dz <= 2) {
                $row = $compass[$dz] . substr($row, 3 * strlen(self::MAP_KEY_MIDDLE));
            }
            $map[] = $row;
        }

        $map[] = implode(" ", array_map(function (string $character, Faction $faction) use ($player): string {
            return LanguageManager::getInstance()->getColorFor($player, $faction) . $character . ": " . $faction->getName();
        }, array_keys($legend), $legend));
        if ($overflown) $map[] = self::MAP_OVERFLOW_MESSAGE;

        return $map;
    }
}