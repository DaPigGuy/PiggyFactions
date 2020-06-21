<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\admin;

use CortexPE\Commando\args\FloatArgument;
use CortexPE\Commando\args\RawStringArgument;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\member\PowerChangeEvent;
use pocketmine\command\CommandSender;

class SetPowerSubCommand extends FactionSubCommand
{
    /** @var bool */
    protected $requiresPlayer = false;

    public function onBasicRun(CommandSender $sender, array $args): void
    {
        $player = $this->plugin->getPlayerManager()->getPlayerByName($args["player"]);
        if ($player === null) {
            $this->plugin->getLanguageManager()->sendMessage($sender, "commands.invalid-player", ["{PLAYER}" => $args["player"]]);
            return;
        }
        $ev = new PowerChangeEvent($player, PowerChangeEvent::CAUSE_ADMIN, (float)$args["power"]);
        $ev->call();
        if ($ev->isCancelled()) return;

        $player->setPower($ev->getPower());
        $this->plugin->getLanguageManager()->sendMessage($sender, "commands.setpower.success", ["{PLAYER}" => $player->getUsername(), "{POWER}" => round($player->getPower(), 2, PHP_ROUND_HALF_DOWN)]);
        $player->sendMessage("commands.setpower.power-set", ["{POWER}" => round($player->getPower(), 2, PHP_ROUND_HALF_DOWN)]);
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("player"));
        $this->registerArgument(1, new FloatArgument("power"));
    }
}