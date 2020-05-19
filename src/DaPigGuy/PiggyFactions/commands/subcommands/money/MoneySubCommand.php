<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\money;

use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class MoneySubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        LanguageManager::getInstance()->sendMessage($sender, "commands.money.balance", ["{MONEY}" => $faction->getMoney()]);
    }
}