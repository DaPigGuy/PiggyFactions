<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use DaPigGuy\PiggyFactions\claims\ClaimsManager;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class ClaimSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $claim = ClaimsManager::getInstance()->getClaim($sender->getLevel(), $sender->getLevel()->getChunkAtPosition($sender));
        if ($claim !== null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.claim.already-claimed");
            return;
        }
        if ($faction->getPower() / $this->plugin->getConfig()->getNested("factions.claim.cost", 1) < count(ClaimsManager::getInstance()->getFactionClaims($faction)) - 1) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.claim.no-power");
            return;
        }
        ClaimsManager::getInstance()->createClaim($faction, $sender->getLevel(), $sender->getLevel()->getChunkAtPosition($sender));
        LanguageManager::getInstance()->sendMessage($sender, "commands.claim.success");
    }
}