<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\admin\powerboost;

use CortexPE\Commando\args\FloatArgument;
use CortexPE\Commando\args\RawStringArgument;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use pocketmine\command\CommandSender;

class PowerBoostPlayerSubCommand extends FactionSubCommand
{
    protected bool $requiresPlayer = false;
    protected ?string $parentNode = "powerboost";

    public function onBasicRun(CommandSender $sender, array $args): void
    {
        $player = $this->plugin->getPlayerManager()->getPlayerByName($args["name"]);
        if ($player === null) {
            $this->plugin->getLanguageManager()->sendMessage($sender, "commands.invalid-player", ["{PLAYER}" => $args["player"]]);
            return;
        }
        $player->setPowerBoost($args["power"]);
        $this->plugin->getLanguageManager()->sendMessage($sender, "commands.powerboost.success-player", ["{PLAYER}" => $player->getUsername(), "{POWER}" => $args["power"]]);
        $player->sendMessage("commands.powerboost.boost-player", ["{POWER}" => $args["power"]]);
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("name"));
        $this->registerArgument(1, new FloatArgument("power"));
    }
}