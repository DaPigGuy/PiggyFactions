<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\admin;

use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\player\Player;

class AdminSubCommand extends FactionSubCommand
{
    /** @var bool */
    protected $requiresFaction = false;

    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $member->setInAdminMode(!$member->isInAdminMode());
        $member->sendMessage("commands.admin.toggled" . ($member->isInAdminMode() ? "" : "-off"));
    }
}