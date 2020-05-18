<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\claims;

use DaPigGuy\PiggyFactions\event\FactionMemberEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\event\Cancellable;
use pocketmine\level\format\Chunk;

class ClaimChunkEvent extends FactionMemberEvent implements Cancellable
{
    /** @var Chunk */
    private $chunk;

    public function __construct(Faction $faction, FactionsPlayer $member, Chunk $chunk)
    {
        parent::__construct($faction, $member);
        $this->chunk = $chunk;
    }

    public function getChunk(): Chunk
    {
        return $this->chunk;
    }
}