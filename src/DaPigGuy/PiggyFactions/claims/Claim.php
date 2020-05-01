<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\claims;

use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\factions\FactionsManager;
use DaPigGuy\PiggyFactions\flags\Flag;
use DaPigGuy\PiggyFactions\PiggyFactions;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;

class Claim
{
    /** @var int */
    private $id;
    /** @var string */
    private $faction;
    /** @var int */
    private $chunkX;
    /** @var int */
    private $chunkZ;
    /** @var string */
    private $level;

    public function __construct(int $id, string $faction, int $chunkX, int $chunkZ, string $level)
    {
        $this->id = $id;
        $this->faction = $faction;
        $this->chunkX = $chunkX;
        $this->chunkZ = $chunkZ;
        $this->level = $level;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFaction(): ?Faction
    {
        return FactionsManager::getInstance()->getFaction($this->faction);
    }

    public function setFaction(Faction $faction): void
    {
        $this->faction = $faction->getId();
        PiggyFactions::getInstance()->getDatabase()->executeChange("piggyfactions.claims.update", ["id" => $this->id, "faction" => $this->faction]);
    }

    public function getLevel(): ?Level
    {
        return PiggyFactions::getInstance()->getServer()->getLevelByName($this->level);
    }

    public function getChunk(): ?Chunk
    {
        $level = PiggyFactions::getInstance()->getServer()->getLevelByName($this->level);
        if ($level === null) return null;
        return $level->getChunk($this->chunkX, $this->chunkZ);
    }

    public function canBeOverClaimed(): bool
    {
        $faction = $this->getFaction();
        if ($faction === null) return false;
        if ($faction->getFlag(Flag::WARZONE) || $faction->getFlag(Flag::SAFEZONE)) return false;
        return $faction->getPower() / PiggyFactions::getInstance()->getConfig()->getNested("factions.claim.cost", 1) < count(ClaimsManager::getInstance()->getFactionClaims($faction));
    }
}