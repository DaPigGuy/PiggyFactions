<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use DaPigGuy\PiggyFactions\PiggyFactions;
use pocketmine\command\CommandSender;

abstract class FactionSubCommand extends BaseSubCommand
{
    /** @var PiggyFactions */
    protected $plugin;

    public function __construct(PiggyFactions $plugin, string $name, string $description = "", array $aliases = [])
    {
        $this->plugin = $plugin;
        $this->setPermission("piggyfactions.command.faction." . $name);
        parent::__construct($name, $description, $aliases);
    }

    public function onFormRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $this->onRun($sender, $aliasUsed, $args);
    }

    protected function prepare(): void
    {
    }
}