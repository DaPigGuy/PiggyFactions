<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\relation;

use DaPigGuy\PiggyFactions\event\FactionEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use pocketmine\event\Cancellable;

class FactionRelationWishEvent extends FactionEvent implements Cancellable
{
    /** @var Faction */
    private $target;
    /** @var string */
    private $wish;

    public function __construct(Faction $faction, Faction $target, string $wish)
    {
        parent::__construct($faction);
        $this->target = $target;
        $this->wish = $wish;
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