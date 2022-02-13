<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use DaPigGuy\PiggyFactions\event\member\FactionLeaveEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\utils\Roles;
use pocketmine\player\Player;

class LeaveSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        if (!$member->isInAdminMode() && $member->getRole() === Roles::LEADER) {
            $member->sendMessage("commands.leave.is-leader");
            return;
        }
        $ev = new FactionLeaveEvent($faction, $member);
        $ev->call();
        if ($ev->isCancelled()) return;

        $faction->removeMember($sender->getUniqueId());
        $member->sendMessage("commands.leave.success");
    }
}