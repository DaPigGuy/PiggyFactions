<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\claims;

use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\utils\AsciiMap;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class MapSubCommand extends FactionSubCommand
{
    protected bool $requiresFaction = false;

    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $sender->sendMessage(implode(TextFormat::EOL, AsciiMap::getMap($sender, AsciiMap::MAP_WIDTH, AsciiMap::MAP_HEIGHT)));
    }
}