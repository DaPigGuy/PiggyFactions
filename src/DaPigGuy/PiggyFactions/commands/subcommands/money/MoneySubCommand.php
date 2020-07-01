<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\money;

use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\player\Player;

class MoneySubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $member->sendMessage("commands.money.balance", ["{MONEY}" => $faction->getMoney()]);
    }
}