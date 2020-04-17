<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use DaPigGuy\PiggyFactions\claims\ClaimsManager;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\players\PlayerManager;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class MapSubCommand extends FactionSubCommand
{
    const MAP_WIDTH = 48;
    const MAP_HEIGHT = 10;
    const MAP_HEIGHT_FULL = 17;

    const MAP_KEY_CHARS = "\\/#?ç¬£$%=&^ABCDEFGHJKLMNOPQRSTUVWXYZÄÖÜÆØÅ1234567890abcdeghjmnopqrsuvwxyÿzäöüæøåâêîûô";
    const MAP_KEY_WILDERNESS = TextFormat::GRAY . "-";
    const MAP_KEY_SEPARATOR = TextFormat::AQUA . "+";

    const MAP_KEY_OVERFLOW = TextFormat::WHITE . "-" . TextFormat::WHITE;
    const MAP_OVERFLOW_MESSAGE = self::MAP_KEY_OVERFLOW . ": Too Many Factions (>" . 107 . ") on this Map.";

    const N = 'N';
    const NE = '/';
    const E = 'E';
    const SE = '\\';
    const S = 'S';
    const SW = '/';
    const W = 'W';
    const NW = '\\';

    /** @var bool */
    protected $requiresFaction = false;

    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $sender->sendMessage(implode(TextFormat::EOL, $this->getMap($sender, self::MAP_WIDTH, self::MAP_HEIGHT, $sender->getYaw())));
    }

    public function getMap(Player $player, int $width, int $height, float $yaw): array
    {
        $center = $player->getLevel()->getChunkAtPosition($player);
        $centerFaction = ($claim = ClaimsManager::getInstance()->getClaim($player->getLevel(), $center)) === null ? null : $claim->getFaction();

        $map = [];

        $head = TextFormat::GREEN . " (" . $center->getX() . "," . $center->getZ() . ") " . ($centerFaction === null ? "Wilderness" : $centerFaction->getName()) . " " . TextFormat::WHITE;
        $head = TextFormat::GOLD . str_repeat("_", (($width - strlen($head)) / 2)) . ".[" . $head . TextFormat::GOLD . "]." . str_repeat("_", (($width - strlen($head)) / 2));
        $map[] = $head;

        $halfWidth = $width / 2;
        $halfHeight = $height / 2;
        $width = $halfWidth * 2 + 1;
        $height = $halfHeight * 2 + 1;

        $topLeft = $player->getLevel()->getChunk($center->getX() - $halfWidth, $center->getZ() - $halfHeight);

        $asciiCompass = $this->getASCIICompass($yaw, TextFormat::RED, TextFormat::GOLD);

        $height--;

        $factions = [];
        $characterIndex = 0;
        $overflown = false;
        $characters = self::MAP_KEY_CHARS;

        for ($dz = 0; $dz < $height; $dz++) {
            $row = "";
            for ($dx = 0; $dx < $width; $dx++) {
                if ($dx == $halfWidth && $dz == $halfHeight) {
                    $row .= self::MAP_KEY_SEPARATOR;
                    continue;
                }

                if (!$overflown && $characterIndex >= strlen(self::MAP_KEY_CHARS)) $overflown = true;
                $chunk = $player->getLevel()->getChunk($topLeft->getX() + $dx, $topLeft->getZ() + $dz);
                $faction = ($claim = ClaimsManager::getInstance()->getClaim($player->getLevel(), $chunk)) === null ? null : $claim->getFaction();
                $contains = in_array($faction, $factions, true);
                if ($faction === null) {
                    $row .= self::MAP_KEY_WILDERNESS;
                } elseif (!$contains && $overflown) {
                    $row .= self::MAP_KEY_OVERFLOW;
                } else {
                    if (!$contains) $factions[$characters{$characterIndex++}] = $faction;
                    $fchar = array_search($faction, $factions);
                    $row .= $this->getColorFor($player, $faction) . $fchar;
                }
            }

            $line = $row;

            if ($dz == 0) $line = $asciiCompass[0] . "" . substr($row, 3 * strlen(self::MAP_KEY_SEPARATOR));
            if ($dz == 1) $line = $asciiCompass[1] . "" . substr($row, 3 * strlen(self::MAP_KEY_SEPARATOR));
            if ($dz == 2) $line = $asciiCompass[2] . "" . substr($row, 3 * strlen(self::MAP_KEY_SEPARATOR));

            $map[] = $line;
        }
        $factionsRow = "";
        foreach ($factions as $char => $faction) {
            $factionsRow .= $this->getColorFor($player, $faction) . $char . ": " . $faction->getName() . " ";
        }
        if ($overflown) $factionsRow .= self::MAP_OVERFLOW_MESSAGE;
        $factionsRow = trim($factionsRow);
        $map[] = $factionsRow;

        return $map;
    }

    public function getColorFor(Player $player, Faction $faction): string
    {
        $playerFaction = PlayerManager::getInstance()->getPlayerFaction($player->getUniqueId());
        if ($playerFaction === $faction) return TextFormat::GREEN;
        return TextFormat::LIGHT_PURPLE;
    }

    public function getASCIICompass(float $degrees, string $colorActive, string $colorDefault): array
    {
        $rows = [];
        $point = self::getCompassPointForDirection($degrees);

        $row = "";
        $row .= ($point === self::NW ? $colorActive : $colorDefault) . self::NW;
        $row .= ($point === self::N ? $colorActive : $colorDefault) . self::N;
        $row .= ($point === self::NE ? $colorActive : $colorDefault) . self::NE;
        $rows[] = $row;

        $row = "";
        $row .= ($point === self::W ? $colorActive : $colorDefault) . self::W;
        $row .= $colorDefault . "+";
        $row .= ($point === self::E ? $colorActive : $colorDefault) . self::E;
        $rows[] = $row;

        $row = "";
        $row .= ($point === self::SW ? $colorActive : $colorDefault) . self::SW;
        $row .= ($point === self::S ? $colorActive : $colorDefault) . self::S;
        $row .= ($point === self::SE ? $colorActive : $colorDefault) . self::SE;
        $rows[] = $row;

        return $rows;
    }

    public function getCompassPointForDirection(float $degrees): ?string
    {
        $degrees = ($degrees - 180) % 360;
        if ($degrees < 0)
            $degrees += 360;

        if (0 <= $degrees && $degrees < 22.5)
            return "N";
        elseif (22.5 <= $degrees && $degrees < 67.5)
            return "NE";
        elseif (67.5 <= $degrees && $degrees < 112.5)
            return "E";
        elseif (112.5 <= $degrees && $degrees < 157.5)
            return "SE";
        elseif (157.5 <= $degrees && $degrees < 202.5)
            return "S";
        elseif (202.5 <= $degrees && $degrees < 247.5)
            return "SW";
        elseif (247.5 <= $degrees && $degrees < 292.5)
            return "W";
        elseif (292.5 <= $degrees && $degrees < 337.5)
            return "NW";
        elseif (337.5 <= $degrees && $degrees < 360.0)
            return "N";
        else
            return null;
    }
}