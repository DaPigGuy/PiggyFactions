<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\claims\unclaim;

use DaPigGuy\PiggyFactions\claims\ClaimsManager;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\claims\UnclaimChunkEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\level\Position;
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
        $positions = $this->getPositions($sender, $args);
        if (empty($positions)) return;
        foreach ($positions as $position) {
            $claim = ClaimsManager::getInstance()->getClaim($position);
            if ($claim !== null) {
                if ($claim->getFaction() === $faction || $member->isInAdminMode()) {
                    $ev = new UnclaimChunkEvent($faction, $member, $claim);
                    $ev->call();
                    if ($ev->isCancelled()) continue;

                    ClaimsManager::getInstance()->deleteClaim($claim);
                    $unclaimed++;
                }
            }
        }
        $member->sendMessage("commands.unclaim.claimed-multiple", ["{AMOUNT}" => $unclaimed, "{COMMAND}" => $this->getName()]);
    }

    /**
     * @return Position[]
     */
    abstract public function getPositions(Player $player, array $args): array;
}