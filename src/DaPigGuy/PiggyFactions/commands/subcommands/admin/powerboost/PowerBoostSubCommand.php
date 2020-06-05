<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\admin\powerboost;

use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use pocketmine\command\CommandSender;

class PowerBoostSubCommand extends FactionSubCommand
{
    /** @var bool */
    protected $requiresPlayer = false;

    public function onBasicRun(CommandSender $sender, array $args): void
    {
        $this->sendUsage();
    }

    protected function prepare(): void
    {
        $this->registerSubCommand(new PowerBoostFactionSubCommand($this->plugin, "faction"));
        $this->registerSubCommand(new PowerBoostPlayerSubCommand($this->plugin, "player"));
    }
}