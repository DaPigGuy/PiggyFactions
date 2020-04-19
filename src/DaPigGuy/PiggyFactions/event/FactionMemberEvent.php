<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event;

use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;

class FactionMemberEvent extends FactionEvent
{
    /** @var FactionsPlayer */
    private $member;

    public function __construct(Faction $faction, FactionsPlayer $member)
    {
        parent::__construct($faction);
        $this->member = $member;
    }

    public function getMember(): FactionsPlayer
    {
        return $this->member;
    }
}