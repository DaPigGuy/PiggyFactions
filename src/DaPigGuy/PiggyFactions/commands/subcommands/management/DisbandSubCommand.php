<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\management;

use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\management\FactionDisbandEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class DisbandSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        if ($member->getRole() !== Faction::ROLE_LEADER) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.not-leader");
            return;
        }
        $ev = new FactionDisbandEvent($faction);
        $ev->call();
        if ($ev->isCancelled()) return;

        $faction->disband();
        LanguageManager::getInstance()->sendMessage($sender, "commands.disband.success");
    }
}