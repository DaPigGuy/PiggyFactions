<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\management;

use CortexPE\Commando\args\TextArgument;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\management\FactionDescriptionChangeEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\player\Player;

class DescriptionSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $ev = new FactionDescriptionChangeEvent($faction, $member, $args["description"]);
        $ev->call();
        if ($ev->isCancelled()) return;

        $faction->setDescription($ev->getDescription());
        $member->sendMessage("commands.description.success", ["{DESCRIPTION}" => $ev->getDescription()]);
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("description"));
    }
}