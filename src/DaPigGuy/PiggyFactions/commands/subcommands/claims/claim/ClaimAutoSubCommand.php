<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\claims\claim;

use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class ClaimAutoSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $member->setAutoClaiming(!$member->isAutoClaiming());
        $member->sendMessage("commands.claim.auto.toggled" . ($member->isAutoClaiming() ? "" : "-off"));
    }

    public function prepare(): void
    {
        $this->setDescription("Claim chunks as you walk automatically");
        $this->setAliases(["a"]);
    }
}