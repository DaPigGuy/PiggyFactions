<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\utils;

use DaPigGuy\PiggyFactions\claims\ClaimsManager;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

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
        $center = $player->getLevel()->getChunkAtPosition($player);
        $centerFaction = ($claim = ClaimsManager::getInstance()->getClaimByPosition($player)) === null ? null : $claim->getFaction();

        $compass = AsciiCompass::getAsciiCompass($player->getYaw());

        $map = [LanguageManager::getInstance()->getMessage(LanguageManager::getInstance()->getPlayerLanguage($player), "commands.map.header", ["{X}" => $center->getX(), "{Z}" => $center->getZ(), "{FACTION}" => $centerFaction === null ? "Wilderness" : $centerFaction->getName()])];

        $legend = [];
        $characterIndex = 0;
        $overflown = false;

        for ($dz = 0; $dz < $height; $dz++) {
            $row = "";
            for ($dx = 0; $dx < $width; $dx++) {
                $chunkX = $center->getX() - ($width / 2) + $dx;
                $chunkZ = $center->getZ() - ($height / 2) + $dz;
                if ($chunkX === $center->getX() && $chunkZ === $center->getZ()) {
                    $row .= self::MAP_KEY_MIDDLE;
                    continue;
                }

                $faction = ($claim = ClaimsManager::getInstance()->getClaim($chunkX, $chunkZ, $player->getLevel()->getFolderName())) === null ? null : $claim->getFaction();

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