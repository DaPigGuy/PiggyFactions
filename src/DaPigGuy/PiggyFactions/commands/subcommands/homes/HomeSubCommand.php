<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\homes;

use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\home\FactionHomeTeleportEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class HomeSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        if (($home = $faction->getHome()) === null) {
            $member->sendMessage("commands.home.not-set");
            return;
        }
        $ev = new FactionHomeTeleportEvent($faction, $sender);
        $ev->call();
        if ($ev->isCancelled()) return;
        $sender->teleport($home);
    }
}