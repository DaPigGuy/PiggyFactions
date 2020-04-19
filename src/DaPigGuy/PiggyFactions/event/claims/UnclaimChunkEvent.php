<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\claims;

use DaPigGuy\PiggyFactions\claims\Claim;
use DaPigGuy\PiggyFactions\event\FactionEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use pocketmine\event\Cancellable;

class UnclaimChunkEvent extends FactionEvent implements Cancellable
{
    /** @var Claim */
    private $claim;

    public function __construct(Faction $faction, Claim $claim)
    {
        parent::__construct($faction);
        $this->claim = $claim;
    }

    public function getClaim(): Claim
    {
        return $this->claim;
    }
}