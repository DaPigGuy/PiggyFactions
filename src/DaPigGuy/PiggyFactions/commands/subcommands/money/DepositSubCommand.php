<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\money;

use CortexPE\Commando\args\FloatArgument;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\player\Player;

class DepositSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        if ($args["money"] < 0) {
            $member->sendMessage("economy.negative-money");
            return;
        }
        $this->plugin->getEconomyProvider()->getMoney($sender, function (float|int $balance) use ($args, $member, $sender, $faction) {
            if ($balance < $args["money"]) {
                $member->sendMessage("economy.not-enough-money", ["{DIFFERENCE}" => $args["money"] - $balance]);
                return;
            }
            $this->plugin->getEconomyProvider()->takeMoney($sender, $args["money"], function (bool $success) use ($member, $args, $faction) {
                if (!$success) {
                    $member->sendMessage("generic-error");
                    return;
                }
                $faction->addMoney($args["money"]);
                $member->sendMessage("commands.deposit.success", ["{MONEY}" => $args["money"]]);
            });

        });
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new FloatArgument("money"));
    }
}