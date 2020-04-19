<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\claims;

use DaPigGuy\PiggyFactions\event\FactionEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use pocketmine\event\Cancellable;
use pocketmine\level\format\Chunk;

class ClaimChunkEvent extends FactionEvent implements Cancellable
{
    /** @var Chunk */
    private $chunk;

    public function __construct(Faction $faction, Chunk $chunk)
    {
        parent::__construct($faction);
        $this->chunk = $chunk;
    }

    public function getChunk(): Chunk
    {
        return $this->chunk;
    }
}