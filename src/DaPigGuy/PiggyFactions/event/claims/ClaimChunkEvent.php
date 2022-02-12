<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\claims;

use DaPigGuy\PiggyFactions\event\FactionMemberEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\event\Cancellable;

class ClaimChunkEvent extends FactionMemberEvent implements Cancellable
{
    private int $chunkX;
    private int $chunkZ;

    public function __construct(Faction $faction, FactionsPlayer $member, int $chunkX, int $chunkZ)
    {
        parent::__construct($faction, $member);
        $this->chunkX = $chunkX;
        $this->chunkZ = $chunkZ;
    }

    public function getChunkX(): int
    {
        return $this->chunkX;
    }

    public function getChunkZ(): int
    {
        return $this->chunkZ;
    }
}