<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\home;

use DaPigGuy\PiggyFactions\event\FactionMemberEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\event\Cancellable;
use pocketmine\world\Position;

class FactionSetHomeEvent extends FactionMemberEvent implements Cancellable
{
    private Position $position;

    public function __construct(Faction $faction, FactionsPlayer $member, Position $position)
    {
        parent::__construct($faction, $member);
        $this->position = $position;
    }

    public function getPosition(): Position
    {
        return $this->position;
    }

    public function setPosition(Position $position): void
    {
        $this->position = $position;
    }
}