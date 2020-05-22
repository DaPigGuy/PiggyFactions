<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\management;

use CortexPE\Commando\args\RawStringArgument;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\management\FactionUnbanEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\players\PlayerManager;
use pocketmine\Player;

class UnbanSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $target = PlayerManager::getInstance()->getPlayerByName($args["name"]);
        if ($target === null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.invalid-player", ["{PLAYER}" => $args["name"]]);
            return;
        }
        if (!$faction->isBanned($target->getUuid())) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.unban.not-banned", ["{PLAYER}" => $target->getUsername()]);
            return;
        }
        $ev = new FactionUnbanEvent($faction, $target, $member);
        $ev->call();
        if ($ev->isCancelled()) return;

        $faction->unbanPlayer($target->getUuid());
        $faction->broadcastMessage("commands.unban.announcement", ["{PLAYER}" => $target->getUsername()]);
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("name"));
    }
}