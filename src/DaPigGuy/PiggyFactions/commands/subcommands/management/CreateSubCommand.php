<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\management;

use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\management\FactionCreateEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\utils\Roles;
use pocketmine\Player;

class CreateSubCommand extends FactionSubCommand
{
    /** @var bool */
    protected $requiresFaction = false;

    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        if ($faction !== null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.already-in-faction");
            return;
        }
        if ($this->plugin->getFactionsManager()->getFactionByName($args["name"]) !== null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.create.name-taken", ["{NAME}" => $args["name"]]);
            return;
        }
        if (in_array(strtolower($args["name"]), $this->plugin->getConfig()->getNested("factions.blacklisted-names", []))) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.create.name-blacklisted", ["{NAME}" => $args["name"]]);
            return;
        }
        if (strlen($args["name"]) > $this->plugin->getConfig()->getNested("factions.max-name-length", 16)) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.create.name-too-long", ["{NAME}" => $args["name"]]);
            return;
        }
        $ev = new FactionCreateEvent($sender, $args["name"]);
        $ev->call();
        if ($ev->isCancelled()) return;

        $this->plugin->getFactionsManager()->createFaction($args["name"], [$sender->getUniqueId()->toString()]);
        $member->setRole(Roles::LEADER);
        LanguageManager::getInstance()->sendMessage($sender, "commands.create.success", ["{NAME}" => $args["name"]]);
    }

    /**
     * @throws ArgumentOrderException
     */
    public function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("name"));
    }
}