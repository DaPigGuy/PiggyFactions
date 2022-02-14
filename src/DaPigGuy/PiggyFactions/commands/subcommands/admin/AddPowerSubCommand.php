<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\admin;

use CortexPE\Commando\args\FloatArgument;
use CortexPE\Commando\args\RawStringArgument;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\member\PowerChangeEvent;
use DaPigGuy\PiggyFactions\utils\RoundValue;
use pocketmine\command\CommandSender;

class AddPowerSubCommand extends FactionSubCommand
{
    protected bool $requiresPlayer = false;

    public function onBasicRun(CommandSender $sender, array $args): void
    {
        $player = $this->plugin->getPlayerManager()->getPlayerByName($args["player"]);
        if ($player === null) {
            $this->plugin->getLanguageManager()->sendMessage($sender, "commands.invalid-player", ["{PLAYER}" => $args["player"]]);
            return;
        }
        $ev = new PowerChangeEvent($player, PowerChangeEvent::CAUSE_ADMIN, $player->getPower() + (float)$args["power"]);
        $ev->call();
        if ($ev->isCancelled()) return;

        $player->setPower($ev->getPower());
        $this->plugin->getLanguageManager()->sendMessage($sender, "commands.addpower.success", ["{PLAYER}" => $player->getUsername(), "{POWER}" => $args["power"]]);
        $player->sendMessage("commands.addpower.power-add", ["{ADDEDPOWER}" => $args["power"], "{POWER}" => RoundValue::round($player->getPower())]);
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("player"));
        $this->registerArgument(1, new FloatArgument("power"));
    }
}