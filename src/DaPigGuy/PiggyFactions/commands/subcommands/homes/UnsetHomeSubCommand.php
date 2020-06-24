<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\homes;

use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\home\FactionUnsetHomeEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class UnsetHomeSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        if ($faction->getHome() === null) {
            $member->sendMessage("commands.home.not-set");
            return;
        }

        $ev = new FactionUnsetHomeEvent($faction, $member);
        $ev->call();
        if ($ev->isCancelled()) return;

        $faction->unsetHome();
        $member->sendMessage("commands.unsethome.success");
    }
}