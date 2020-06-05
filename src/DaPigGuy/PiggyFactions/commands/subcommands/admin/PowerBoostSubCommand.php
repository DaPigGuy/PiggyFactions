<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\admin;

use CortexPE\Commando\args\FloatArgument;
use CortexPE\Commando\args\RawStringArgument;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\factions\FactionsManager;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\PlayerManager;
use pocketmine\command\CommandSender;

class PowerBoostSubCommand extends FactionSubCommand
{
    /** @var bool */
    protected $requiresPlayer = false;

    public function onBasicRun(CommandSender $sender, array $args): void
    {
        $target = $args["target"];
        if ($target === "f" || $target === "faction") {
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
        if ($target === "p" || $target === "player") {
            $player = PlayerManager::getInstance()->getPlayerByName($args["name"]);
            if ($player === null) {
                LanguageManager::getInstance()->sendMessage($sender, "commands.invalid-player", ["{PLAYER}" => $args["player"]]);
                return;
            }
            $player->setPowerBoost($args["power"]);
            LanguageManager::getInstance()->sendMessage($sender, "commands.powerboost.success-player", ["{PLAYER}" => $player->getUsername(), "{POWER}" => $args["power"]]);
            $player->sendMessage("commands.powerboost.boost-player", ["{POWER}" => $args["power"]]);
            return;
        }
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("target"));
        $this->registerArgument(1, new RawStringArgument("name"));
        $this->registerArgument(2, new FloatArgument("power"));
    }
}