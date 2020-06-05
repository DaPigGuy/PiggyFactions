<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\admin\powerboost;

use CortexPE\Commando\args\FloatArgument;
use CortexPE\Commando\args\RawStringArgument;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\factions\FactionsManager;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use pocketmine\command\CommandSender;

class PowerBoostFactionSubCommand extends FactionSubCommand
{
    /** @var bool */
    protected $requiresPlayer = false;

    public function onBasicRun(CommandSender $sender, array $args): void
    {
        $faction = FactionsManager::getInstance()->getFactionByName($args["name"]);
        if ($faction === null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.invalid-faction", ["{FACTION}" => $args["name"]]);
            return;
        }
        $faction->setPowerBoost($args["power"]);
        LanguageManager::getInstance()->sendMessage($sender, "commands.powerboost.success-faction", ["{FACTION}" => $faction->getName(), "{POWER}" => $args["power"]]);
        $faction->broadcastMessage("commands.powerboost.boost-faction", ["{POWER}" => $args["power"]]);
        return;
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("name"));
        $this->registerArgument(1, new FloatArgument("power"));
        $this->setDescription("Increases faction maximum power");
        $this->setAliases(["f"]);
    }
}