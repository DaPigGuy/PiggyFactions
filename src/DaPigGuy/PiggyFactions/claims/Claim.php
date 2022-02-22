<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\claims;

use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\factions\FactionsManager;
use DaPigGuy\PiggyFactions\flags\Flag;
use DaPigGuy\PiggyFactions\PiggyFactions;
use pocketmine\world\World;

class Claim
{
    public function __construct(private string $faction, private int $chunkX, private int $chunkZ, private string $level)
    {
    }

    public function getFaction(): ?Faction
    {
        return FactionsManager::getInstance()->getFaction($this->faction);
    }

    public function setFaction(Faction $faction): void
    {
        $this->faction = $faction->getId();
        PiggyFactions::getInstance()->getDatabase()->executeChange("piggyfactions.claims.update", ["chunkX" => $this->chunkX, "chunkZ" => $this->chunkZ, "level" => $this->level, "faction" => $this->faction]);
    }

    public function getLevel(): ?World
    {
        return PiggyFactions::getInstance()->getServer()->getWorldManager()->getWorldByName($this->level);
    }

    public function getChunkX(): int
    {
        return $this->chunkX;
    }

    public function getChunkZ(): int
    {
        return $this->chunkZ;
    }

    public function canBeOverClaimed(): bool
    {
        $faction = $this->getFaction();
        return PiggyFactions::getInstance()->getConfig()->getNested("factions.claim.overclaim", true) &&
            $faction !== null &&
            !$faction->getFlag(Flag::WARZONE) &&
            !$faction->getFlag(Flag::SAFEZONE) &&
            $faction->getPower() / PiggyFactions::getInstance()->getConfig()->getNested("factions.claim.cost", 1) < count(ClaimsManager::getInstance()->getFactionClaims($faction));
    }
}