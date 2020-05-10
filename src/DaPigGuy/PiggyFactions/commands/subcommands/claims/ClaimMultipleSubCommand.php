<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\claims;

use DaPigGuy\PiggyFactions\claims\ClaimsManager;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\claims\ClaimChunkEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\level\format\Chunk;
use pocketmine\Player;

abstract class ClaimMultipleSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        if (in_array($sender->getLevel()->getFolderName(), $this->plugin->getConfig()->getNested("factions.claims.blacklisted-worlds"))) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.claim.blacklisted-world");
            return;
        }
        $claimed = 0;
        $chunks = $this->getChunks($sender, $args);
        if (empty($chunks)) return;
        foreach ($chunks as $chunk) {
            if (!$member->isInAdminMode()) {
                if ($faction->getPower() / $this->plugin->getConfig()->getNested("factions.claim.cost", 1) < count(ClaimsManager::getInstance()->getFactionClaims($faction)) + 1) {
                    break;
                }
                if (count(ClaimsManager::getInstance()->getFactionClaims($faction)) >= ($max = $this->plugin->getConfig()->getNested("factions.claims.max", -1)) && $max !== -1) {
                    break;
                }
            }
            $claim = ClaimsManager::getInstance()->getClaim($sender->getLevel(), $chunk);
            if ($claim !== null) {
                if ($this->plugin->getConfig()->getNested("factions.claim.overclaim", true) && ($claim->canBeOverClaimed() || $member->isInAdminMode()) && $claim->getFaction() !== $faction) {
                    $claim->setFaction($faction);
                    $claimed++;
                    continue;
                }
            }

            $ev = new ClaimChunkEvent($faction, $chunk);
            $ev->call();
            if ($ev->isCancelled()) continue;

            ClaimsManager::getInstance()->createClaim($faction, $sender->getLevel(), $chunk);
            $claimed++;
        }
        LanguageManager::getInstance()->sendMessage($sender, "commands.claim.claimed-multiple", ["{AMOUNT}" => $claimed, "{COMMAND}" => $this->getName()]);
    }

    /**
     * @return Chunk[]
     */
    abstract public function getChunks(Player $player, array $args): array;
}