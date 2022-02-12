<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\claims\claim;

use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\player\Player;

class ClaimAutoSubCommand extends FactionSubCommand
{
    /** @var string */
    protected $parentNode = "claim";

    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $member->setAutoClaiming(!$member->isAutoClaiming());
        $member->sendMessage("commands.claim.auto.toggled" . ($member->isAutoClaiming() ? "" : "-off"));
    }
}