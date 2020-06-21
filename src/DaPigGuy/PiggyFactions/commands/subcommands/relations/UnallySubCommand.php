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

class UnallySubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $targetFaction = $this->plugin->getFactionsManager()->getFactionByName($args["faction"]);
        if ($targetFaction === null) {
            $member->sendMessage("commands.invalid-faction", ["{FACTION}" => $args["faction"]]);
            return;
        }
        if (!$targetFaction->isAllied($faction)) {
            $member->sendMessage("commands.unally.not-allied", ["{FACTION}" => $targetFaction->getName()]);
            return;
        }
        $ev = new FactionRelationEvent($faction, $targetFaction, Relations::NONE);
        $ev->call();
        if ($ev->isCancelled()) return;

        $faction->setRelation($targetFaction, Relations::NONE);
        $targetFaction->setRelation($faction, Relations::NONE);
        $faction->broadcastMessage("commands.unally.unallied", ["{FACTION}" => $targetFaction->getName()]);
        $targetFaction->broadcastMessage("commands.unally.unallied", ["{FACTION}" => $faction->getName()]);
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("faction"));
    }
}