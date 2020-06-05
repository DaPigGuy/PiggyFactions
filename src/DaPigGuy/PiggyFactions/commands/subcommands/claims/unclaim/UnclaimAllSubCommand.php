<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\claims\unclaim;

use DaPigGuy\PiggyFactions\claims\ClaimsManager;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\claims\UnclaimAllChunksEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class UnclaimAllSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        if ($member->getFaction() !== $faction && !$member->isInAdminMode()) {
            $member->sendMessage("commands.unclaim.other-faction");
            return;
        }
        $ev = new UnclaimAllChunksEvent($faction, $member);
        $ev->call();
        if ($ev->isCancelled()) return;

        foreach (ClaimsManager::getInstance()->getFactionClaims($faction) as $claim) {
            ClaimsManager::getInstance()->deleteClaim($claim);
        }
        $member->sendMessage("commands.unclaim.all.success");
        return;
    }

    public function prepare(): void
    {
        $this->setDescription("Unclaims all claimed chunks");
    }
}