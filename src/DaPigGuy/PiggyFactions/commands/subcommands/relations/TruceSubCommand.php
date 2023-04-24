<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\relations;

use DaPigGuy\PiggyFactions\libs\CortexPE\Commando\args\TextArgument;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\relation\FactionRelationEvent;
use DaPigGuy\PiggyFactions\event\relation\FactionRelationWishEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\utils\Relations;
use pocketmine\player\Player;

class TruceSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $targetFaction = $this->plugin->getFactionsManager()->getFactionByName($args["faction"]);
        if ($targetFaction === null) {
            $member->sendMessage("commands.invalid-faction", ["{FACTION}" => $args["faction"]]);
            return;
        }
        if ($targetFaction->getId() === $faction->getId()) {
            $member->sendMessage("commands.truce.self");
            return;
        }
        if ($targetFaction->isTruced($faction)) {
            $member->sendMessage("already-truced");
            return;
        }
        if ($targetFaction->getRelationWish($faction) === Relations::TRUCE) {
            $ev = new FactionRelationEvent($faction, $targetFaction, Relations::TRUCE);
            $ev->call();
            if ($ev->isCancelled()) return;

            $targetFaction->revokeRelationWish($faction);
            $faction->setRelation($targetFaction, Relations::TRUCE);
            $targetFaction->setRelation($faction, Relations::TRUCE);
            $faction->broadcastMessage("commands.truce.truced", ["{TRUCED}" => $targetFaction->getName()]);
            $targetFaction->broadcastMessage("commands.truce.truced", ["{TRUCED}" => $faction->getName()]);
            return;
        }
        $ev = new FactionRelationWishEvent($faction, $targetFaction, Relations::TRUCE);
        $ev->call();
        if ($ev->isCancelled()) return;

        $faction->setRelationWish($targetFaction, Relations::TRUCE);
        $member->sendMessage("commands.truce.success", ["{FACTION}" => $targetFaction->getName()]);
        $targetFaction->broadcastMessage("commands.truce.request", ["{FACTION}" => $faction->getName()]);
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("faction"));
    }
}