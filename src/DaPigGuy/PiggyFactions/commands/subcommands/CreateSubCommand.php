<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use pocketmine\Player;

class CreateSubCommand extends FactionSubCommand
{
    /** @var bool */
    protected $requiresFaction = false;

    public function onNormalRun(Player $sender, ?Faction $faction, string $aliasUsed, array $args): void
    {
        if ($faction !== null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.already-in-faction");
            return;
        }
        if ($this->plugin->getFactionsManager()->getFactionByName($args["name"]) !== null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.create.name-taken", ["{NAME}" => $args["name"]]);
            return;
        }
        $this->plugin->getFactionsManager()->createFaction($args["name"], $sender);
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