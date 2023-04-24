<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\admin;

use DaPigGuy\PiggyFactions\libs\CortexPE\Commando\args\FloatArgument;
use DaPigGuy\PiggyFactions\libs\CortexPE\Commando\args\RawStringArgument;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\member\PowerChangeEvent;
use DaPigGuy\PiggyFactions\utils\RoundValue;
use pocketmine\command\CommandSender;

class SetPowerSubCommand extends FactionSubCommand
{
    protected bool $requiresPlayer = false;

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
        $this->plugin->getLanguageManager()->sendMessage($sender, "commands.setpower.success", ["{PLAYER}" => $player->getUsername(), "{POWER}" => RoundValue::round($player->getPower())]);
        $player->sendMessage("commands.setpower.power-set", ["{POWER}" => RoundValue::round($player->getPower())]);
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("player"));
        $this->registerArgument(1, new FloatArgument("power"));
    }
}