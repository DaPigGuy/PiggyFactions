<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\claims;

use DaPigGuy\PiggyFactions\claims\Claim;
use DaPigGuy\PiggyFactions\event\FactionMemberEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\event\Cancellable;

class ChunkOverclaimEvent extends FactionMemberEvent implements Cancellable
{
    public function __construct(Faction $faction, FactionsPlayer $member, private Claim $claim)
    {
        parent::__construct($faction, $member);
    }

    public function getClaim(): Claim
    {
        return $this->claim;
    }
}