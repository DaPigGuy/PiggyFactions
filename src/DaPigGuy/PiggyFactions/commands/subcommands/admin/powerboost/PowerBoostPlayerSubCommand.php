<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\admin\powerboost;

use CortexPE\Commando\args\FloatArgument;
use CortexPE\Commando\args\RawStringArgument;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\PlayerManager;
use pocketmine\command\CommandSender;

class PowerBoostPlayerSubCommand extends FactionSubCommand
{
    /** @var bool */
    protected $requiresPlayer = false;

    public function onBasicRun(CommandSender $sender, array $args): void
    {
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

    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("name"));
        $this->registerArgument(1, new FloatArgument("power"));
    }
}