<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\claims\unclaim;

use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\player\Player;

class UnclaimAutoSubCommand extends FactionSubCommand
{
    protected ?string $parentNode = "unclaim";

    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        if (in_array($sender->getWorld()->getFolderName(), $this->plugin->getConfig()->getNested("factions.claims.blacklisted-worlds"))) {
            $member->sendMessage("commands.unclaim.blacklisted-world");
            return;
        }
        $member->setAutoUnclaiming(!$member->isAutoUnclaiming());
        $member->sendMessage("commands.unclaim.auto.toggled" . ($member->isAutoUnclaiming() ? "" : "-off"));
    }
}