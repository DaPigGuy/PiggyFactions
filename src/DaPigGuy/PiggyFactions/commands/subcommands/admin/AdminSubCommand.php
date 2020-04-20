<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\admin;

use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class AdminSubCommand extends FactionSubCommand
{
    /** @var bool */
    protected $requiresFaction = false;

    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $member->setInAdminMode(!$member->isInAdminMode());
        LanguageManager::getInstance()->sendMessage($sender, "commands.admin.toggled" . ($member->isInAdminMode() ? "" : "-off"));
    }
}