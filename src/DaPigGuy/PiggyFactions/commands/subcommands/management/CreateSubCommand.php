<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\management;

use DaPigGuy\PiggyFactions\libs\CortexPE\Commando\args\TextArgument;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\management\FactionCreateEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\utils\Roles;
use pocketmine\player\Player;

class CreateSubCommand extends FactionSubCommand
{
    protected bool $requiresFaction = false;

    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        if ($faction !== null) {
            $member->sendMessage("commands.already-in-faction");
            return;
        }
        if ($this->plugin->getFactionsManager()->getFactionByName($args["name"]) !== null) {
            $member->sendMessage("commands.create.name-taken", ["{NAME}" => $args["name"]]);
            return;
        }
        if ($this->plugin->getConfig()->getNested("factions.enforce-alphanumeric-names", false) && !ctype_alnum($args["name"])) {
            $member->sendMessage("commands.create.alphanumeric-only", ["{NAME}" => $args["name"]]);
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
        $ev = new FactionCreateEvent($sender, $args["name"]);
        $ev->call();
        if ($ev->isCancelled()) return;

        $this->plugin->getFactionsManager()->createFaction($args["name"], [$sender->getUniqueId()->toString()]);
        $member->setRole(Roles::LEADER);
        $member->sendMessage("commands.create.success", ["{NAME}" => $args["name"]]);
    }

    public function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("name"));
    }
}