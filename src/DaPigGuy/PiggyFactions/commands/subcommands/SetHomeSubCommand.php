<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use pocketmine\Player;

class SetHomeSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, string $aliasUsed, array $args): void
    {
        if (!$faction->hasPermission($faction->getMember($sender->getName()), $this->getName())) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.no-permission");
            return;
        }
        $faction->setHome($sender->asPosition());
        LanguageManager::getInstance()->sendMessage($sender, "commands.sethome.success");
    }
}