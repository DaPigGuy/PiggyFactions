<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\relations;

use CortexPE\Commando\args\TextArgument;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\relation\FactionRelationEvent;
use DaPigGuy\PiggyFactions\event\relation\FactionRelationWishEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\utils\Relations;
use pocketmine\Player;

class AllySubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $targetFaction = $this->plugin->getFactionsManager()->getFactionByName($args["faction"]);
        if ($targetFaction === null) {
            $member->sendMessage("commands.invalid-faction", ["{FACTION}" => $args["faction"]]);
            return;
        }
        if ($targetFaction->getId() === $faction->getId()) {
            $member->sendMessage("commands.ally.self");
            return;
        }
        if ($targetFaction->isAllied($faction)) {
            $member->sendMessage("already-allied");
            return;
        }
        if ($targetFaction->getRelationWish($faction) === Relations::ALLY) {
            $ev = new FactionRelationEvent($faction, $targetFaction, Relations::ALLY);
            $ev->call();
            if ($ev->isCancelled()) return;

            $targetFaction->revokeRelationWish($faction);
            $faction->setRelation($targetFaction, Relations::ALLY);
            $targetFaction->setRelation($faction, Relations::ALLY);
            $faction->broadcastMessage("commands.ally.allied", ["{ALLY}" => $targetFaction->getName()]);
            $targetFaction->broadcastMessage("commands.ally.allied", ["{ALLY}" => $faction->getName()]);
            return;
        }
        $ev = new FactionRelationWishEvent($faction, $targetFaction, Relations::ALLY);
        $ev->call();
        if ($ev->isCancelled()) return;

        $faction->setRelationWish($targetFaction, Relations::ALLY);
        $member->sendMessage("commands.ally.success", ["{FACTION}" => $targetFaction->getName()]);
        $targetFaction->broadcastMessage("commands.ally.request", ["{FACTION}" => $faction->getName()]);
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("faction"));
    }
}