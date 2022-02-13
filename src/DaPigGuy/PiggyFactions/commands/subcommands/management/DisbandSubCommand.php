<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\management;

use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\management\FactionDisbandEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\utils\Roles;
use pocketmine\player\Player;

class DisbandSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        if ($member->getRole() !== Roles::LEADER && !$member->isInAdminMode()) {
            $member->sendMessage("commands.not-leader");
            return;
        }
        $ev = new FactionDisbandEvent($faction);
        $ev->call();
        if ($ev->isCancelled()) return;

        $faction->disband();
        $member->sendMessage("commands.disband.success");
    }
}