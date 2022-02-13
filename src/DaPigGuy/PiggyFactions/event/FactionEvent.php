<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event;

use DaPigGuy\PiggyFactions\factions\Faction;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;

abstract class FactionEvent extends Event
{
    use CancellableTrait;

    private Faction $faction;

    public function __construct(Faction $faction)
    {
        $this->faction = $faction;
    }

    public function getFaction(): Faction
    {
        return $this->faction;
    }
}