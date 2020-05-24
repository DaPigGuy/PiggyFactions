<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\management;

use CortexPE\Commando\args\TextArgument;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\management\FactionRenameEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class NameSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        if ($this->plugin->getFactionsManager()->getFactionByName($args["name"]) !== null) {
            $member->sendMessage("commands.create.name-taken", ["{NAME}" => $args["name"]]);
            return;
        }
        if (in_array(strtolower($args["name"]), $this->plugin->getConfig()->getNested("factions.blacklisted-names", []))) {
            $member->sendMessage("commands.create.name-blacklisted", ["{NAME}" => $args["name"]]);
            return;
        }
        if (strlen($args["name"]) > $this->plugin->getConfig()->getNested("factions.max-name-length", 16)) {
            $member->sendMessage("commands.create.name-too-long", ["{NAME}" => $args["name"]]);
            return;
        }
        $ev = new FactionRenameEvent($faction, $member, $args["name"]);
        $ev->call();
        if ($ev->isCancelled()) return;

        $faction->setName($ev->getName());
        $member->sendMessage("commands.name.success");
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("name"));
    }
}