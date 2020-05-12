<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use DaPigGuy\PiggyFactions\claims\ClaimsManager;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\permissions\FactionPermission;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class FlySubCommand extends FactionSubCommand
{
    /** @var bool */
    protected $factionPermission = false;

    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $claim = ClaimsManager::getInstance()->getClaim($sender->getLevel(), $sender->getLevel()->getChunkAtPosition($sender));
        if ($claim === null || (!$claim->getFaction()->hasPermission($member, FactionPermission::FLY))) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.fly.not-allowed");
            return;
        }
        $member->setFlying(!$member->isFlying());
        $sender->setAllowFlight($member->isFlying());
        if (!$member->isFlying()) $sender->setFlying(false);
        LanguageManager::getInstance()->sendMessage($sender, "commands.fly.toggled" . ($member->isFlying() ? "" : "-off"));
    }
}