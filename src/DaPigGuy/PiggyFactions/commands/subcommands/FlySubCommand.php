<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\permissions\FactionPermission;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\tasks\CheckFlightTask;
use pocketmine\player\Player;

class FlySubCommand extends FactionSubCommand
{
    /** @var bool */
    protected $factionPermission = false;

    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $claim = $this->plugin->getClaimsManager()->getClaimByPosition($sender->getPosition());
        if ($claim === null || (!$claim->getFaction()->hasPermission($member, FactionPermission::FLY))) {
            $member->sendMessage("commands.fly.not-allowed");
            return;
        }
        $member->setFlying(!$member->isFlying());
        $sender->setAllowFlight($member->isFlying());
        if (!$member->isFlying()) $sender->setFlying(false);
        $this->plugin->getScheduler()->scheduleRepeatingTask(new CheckFlightTask($sender, $member), 20);
        $member->sendMessage("commands.fly.toggled" . ($member->isFlying() ? "" : "-off"));
    }
}