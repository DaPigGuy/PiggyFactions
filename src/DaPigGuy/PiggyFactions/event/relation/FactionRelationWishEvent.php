<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\relation;

use DaPigGuy\PiggyFactions\event\FactionEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use pocketmine\event\Cancellable;

class FactionRelationWishEvent extends FactionEvent implements Cancellable
{
    public function __construct(Faction $faction, private Faction $target, private string $wish)
    {
        parent::__construct($faction);
    }

    public function getTarget(): Faction
    {
        return $this->target;
    }

    public function getWish(): string
    {
        return $this->wish;
    }
}