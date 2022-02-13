<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\homes;

use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\home\FactionHomeTeleportEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\player\Player;

class HomeSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        if (!$faction->getHomeWorld()) {
            $member->sendMessage("commands.home.world-not-found");
            return;
        }
        if (!($home = $faction->getHome())) {
            $member->sendMessage("commands.home.not-set");
            return;
        }

        $ev = new FactionHomeTeleportEvent($faction, $sender);
        $ev->call();
        if ($ev->isCancelled()) return;
        $sender->teleport($home);
    }
}