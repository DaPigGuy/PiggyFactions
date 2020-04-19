<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\home;

use DaPigGuy\PiggyFactions\event\FactionEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use pocketmine\event\Cancellable;
use pocketmine\level\Position;

class FactionSetHomeEvent extends FactionEvent implements Cancellable
{
    /** @var Position */
    private $position;

    public function __construct(Faction $faction, Position $position)
    {
        parent::__construct($faction);
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