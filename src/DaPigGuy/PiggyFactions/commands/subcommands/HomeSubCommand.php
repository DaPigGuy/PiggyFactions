<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use pocketmine\Player;

class HomeSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, string $aliasUsed, array $args): void
    {
        if (($home = $faction->getHome()) === null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.home.not-set");
            return;
        }
        $sender->teleport($home);
    }
}