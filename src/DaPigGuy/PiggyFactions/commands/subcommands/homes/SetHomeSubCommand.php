<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\homes;

use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\home\FactionSetHomeEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\player\Player;

class SetHomeSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        if ($this->plugin->getConfig()->getNested("factions.homes.within-territory", true)) {
            $claim = $this->plugin->getClaimsManager()->getClaimByPosition($sender->getPosition());
            if ($claim === null || $claim->getFaction()->getId() !== $faction->getId()) {
                $member->sendMessage("commands.sethome.not-within-territory");
                return;
            }
        }

        $ev = new FactionSetHomeEvent($faction, $member, $sender->getPosition());
        $ev->call();
        if ($ev->isCancelled()) return;

        $faction->setHome($ev->getPosition());
        $member->sendMessage("commands.sethome.success");
    }
}