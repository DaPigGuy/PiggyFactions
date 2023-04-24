<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\admin\powerboost;

use DaPigGuy\PiggyFactions\libs\CortexPE\Commando\args\FloatArgument;
use DaPigGuy\PiggyFactions\libs\CortexPE\Commando\args\RawStringArgument;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use pocketmine\command\CommandSender;

class PowerBoostFactionSubCommand extends FactionSubCommand
{
    protected bool $requiresPlayer = false;
    protected ?string $parentNode = "powerboost";

    public function onBasicRun(CommandSender $sender, array $args): void
    {
        $faction = $this->plugin->getFactionsManager()->getFactionByName($args["name"]);
        if ($faction === null) {
            $this->plugin->getLanguageManager()->sendMessage($sender, "commands.invalid-faction", ["{FACTION}" => $args["name"]]);
            return;
        }
        $faction->setPowerBoost($args["power"]);
        $this->plugin->getLanguageManager()->sendMessage($sender, "commands.powerboost.success-faction", ["{FACTION}" => $faction->getName(), "{POWER}" => $args["power"]]);
        $faction->broadcastMessage("commands.powerboost.boost-faction", ["{POWER}" => $args["power"]]);
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("name"));
        $this->registerArgument(1, new FloatArgument("power"));
    }
}