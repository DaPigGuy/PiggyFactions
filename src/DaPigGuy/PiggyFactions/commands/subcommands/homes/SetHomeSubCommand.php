<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\homes;

use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\home\FactionSetHomeEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class SetHomeSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $ev = new FactionSetHomeEvent($faction, $sender->asPosition());
        $ev->call();
        if ($ev->isCancelled()) return;

        $faction->setHome($ev->getPosition());
        LanguageManager::getInstance()->sendMessage($sender, "commands.sethome.success");
    }
}