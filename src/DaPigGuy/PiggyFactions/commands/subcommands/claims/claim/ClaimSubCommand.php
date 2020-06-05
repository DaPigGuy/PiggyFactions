<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\claims\claim;

use DaPigGuy\PiggyFactions\claims\ClaimsManager;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\claims\ChunkOverclaimEvent;
use DaPigGuy\PiggyFactions\event\claims\ClaimChunkEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\level\format\Chunk;
use pocketmine\Player;

class ClaimSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        if (in_array($sender->getLevel()->getFolderName(), $this->plugin->getConfig()->getNested("factions.claims.blacklisted-worlds"))) {
            $member->sendMessage("commands.claim.blacklisted-world");
            return;
        }
        if (!$member->isInAdminMode()) {
            if ($faction->getPower() / $this->plugin->getConfig()->getNested("factions.claim.cost", 1) < count(ClaimsManager::getInstance()->getFactionClaims($faction)) + 1) {
                $member->sendMessage("commands.claim.no-power");
                return;
            }
            if (count(ClaimsManager::getInstance()->getFactionClaims($faction)) >= ($max = $this->plugin->getConfig()->getNested("factions.claims.max", -1)) && $max !== -1) {
                $member->sendMessage("commands.claim.max-claimed");
                return;
            }
        }
        $claim = ClaimsManager::getInstance()->getClaim($sender->getLevel(), $sender->getLevel()->getChunkAtPosition($sender));
        if ($claim !== null) {
            if ($claim->canBeOverClaimed() && $claim->getFaction() !== $faction) {
                $adjacentChunks = $sender->getLevel()->getAdjacentChunks($claim->getChunk()->getX(), $claim->getChunk()->getZ());
                foreach ($adjacentChunks as $chunk) {
                    if ($chunk instanceof Chunk) {
                        $adjacentClaim = ClaimsManager::getInstance()->getClaim($sender->getLevel(), $sender->getLevel()->getChunk($chunk->getX(), $chunk->getZ()));
                        if ($adjacentClaim !== null && $adjacentClaim->getFaction() === $faction) {
                            $ev = new ChunkOverclaimEvent($faction, $member, $claim);
                            $ev->call();
                            if ($ev->isCancelled()) return;

                            $member->sendMessage("commands.claim.over-claimed");
                            $claim->setFaction($faction);
                            return;
                        }
                    }
                }
            }
            $member->sendMessage("commands.claim.already-claimed");
            return;
        }
        $ev = new ClaimChunkEvent($faction, $member, $sender->getLevel()->getChunkAtPosition($sender));
        $ev->call();
        if ($ev->isCancelled()) return;

        ClaimsManager::getInstance()->createClaim($faction, $sender->getLevel(), $sender->getLevel()->getChunkAtPosition($sender));
        $member->sendMessage("commands.claim.success");
    }

    protected function prepare(): void
    {
        $this->registerSubCommand(new ClaimAutoSubCommand($this->plugin, "auto"));
        $this->registerSubCommand(new ClaimCircleSubCommand($this->plugin, "circle"));
        $this->registerSubCommand(new ClaimSquareSubCommand($this->plugin, "square"));
    }
}