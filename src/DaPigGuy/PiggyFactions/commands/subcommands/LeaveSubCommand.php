<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use pocketmine\Player;

class LeaveSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, string $aliasUsed, array $args): void
    {
        if ($faction->getMember($sender->getName())->getRole() === Faction::ROLE_LEADER) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.leave.is-leader");
            return;
        }
        $faction->removeMember($sender->getUniqueId());
        LanguageManager::getInstance()->sendMessage($sender, "commands.leave.success");
    }
}