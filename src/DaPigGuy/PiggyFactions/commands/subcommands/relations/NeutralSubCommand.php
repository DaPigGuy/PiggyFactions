<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\relations;

use CortexPE\Commando\args\TextArgument;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\relation\FactionRelationEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\utils\Relations;
use pocketmine\Player;

class NeutralSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $targetFaction = $this->plugin->getFactionsManager()->getFactionByName($args["faction"]);
        if ($targetFaction === null) {
            $member->sendMessage("commands.invalid-faction", ["{FACTION}" => $args["faction"]]);
            return;
        }
        if ($faction->getRelation($targetFaction) === Relations::NONE) {
            $member->sendMessage("commands.neutral.already-neutral", ["{FACTION}" => $targetFaction->getName()]);
            return;
        }
        $ev = new FactionRelationEvent($faction, $targetFaction, Relations::NONE);
        $ev->call();
        if ($ev->isCancelled()) return;

        $faction->setRelation($targetFaction, Relations::NONE);
        $faction->broadcastMessage("commands.neutral.success", ["{FACTION}" => $targetFaction->getName()]);
        if ($targetFaction->isAllied($faction) || $targetFaction->isTruced($faction)) {
            $targetFaction->broadcastMessage("commands.neutral.success", ["{FACTION}" => $faction->getName()]);
            $targetFaction->setRelation($faction, Relations::NONE);
        }
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("faction"));
    }
}