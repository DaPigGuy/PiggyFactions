<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\management;

use CortexPE\Commando\args\TextArgument;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\management\FactionCreateEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\utils\Roles;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class CreateSubCommand extends FactionSubCommand
{
    protected bool $requiresFaction = false;

    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $factionName = TextFormat::clean($args["name"]);

        if ($faction !== null) {
            $member->sendMessage("commands.already-in-faction");
            return;
        }
        if ($this->plugin->getFactionsManager()->getFactionByName($factionName) !== null) {
            $member->sendMessage("commands.create.name-taken", ["{NAME}" => $factionName]);
            return;
        }
        if ($this->plugin->getConfig()->getNested("factions.enforce-alphanumeric-names", false) && !ctype_alnum($factionName)) {
            $member->sendMessage("commands.create.alphanumeric-only", ["{NAME}" => $factionName]);
            return;
        }
        if (in_array(strtolower($factionName), $this->plugin->getConfig()->getNested("factions.blacklisted-names", []))) {
            $member->sendMessage("commands.create.name-blacklisted", ["{NAME}" => $factionName]);
            return;
        }
        if (strlen($factionName) > $this->plugin->getConfig()->getNested("factions.max-name-length", 16)) {
            $member->sendMessage("commands.create.name-too-long", ["{NAME}" => $factionName]);
            return;
        }
        $ev = new FactionCreateEvent($sender, $factionName);
        $ev->call();
        if ($ev->isCancelled()) return;

        $this->plugin->getFactionsManager()->createFaction($factionName, [$sender->getUniqueId()->toString()]);
        $member->setRole(Roles::LEADER);
        $member->sendMessage("commands.create.success", ["{NAME}" => $factionName]);
    }

    public function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("name"));
    }
}