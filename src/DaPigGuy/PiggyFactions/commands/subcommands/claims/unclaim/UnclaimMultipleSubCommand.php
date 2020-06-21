<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\claims\unclaim;

use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\claims\UnclaimChunkEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

abstract class UnclaimMultipleSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        if (in_array($sender->getLevel()->getFolderName(), $this->plugin->getConfig()->getNested("factions.claims.blacklisted-worlds"))) {
            $member->sendMessage("commands.unclaim.blacklisted-world");
            return;
        }
        $unclaimed = 0;
        $chunks = $this->getChunks($sender, $args);
        if (empty($chunks)) return;
        foreach ($chunks as $chunk) {
            $claim = $this->plugin->getClaimsManager()->getClaim($chunk[0], $chunk[1], $sender->getLevel()->getFolderName());
            if ($claim !== null) {
                if ($claim->getFaction() === $faction || $member->isInAdminMode()) {
                    $ev = new UnclaimChunkEvent($faction, $member, $claim);
                    $ev->call();
                    if ($ev->isCancelled()) continue;

                    $this->plugin->getClaimsManager()->deleteClaim($claim);
                    $unclaimed++;
                }
            }
        }
        $member->sendMessage("commands.unclaim.claimed-multiple", ["{AMOUNT}" => $unclaimed, "{COMMAND}" => $this->getName()]);
    }

    abstract public function getChunks(Player $player, array $args): array;
}