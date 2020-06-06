<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\relation;

use DaPigGuy\PiggyFactions\event\FactionEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use pocketmine\event\Cancellable;

class FactionRelationEvent extends FactionEvent implements Cancellable
{
    /** @var Faction */
    private $targetFaction;
    /** @var string */
    private $relation;

    public function __construct(Faction $faction, Faction $targetFaction, string $relation)
    {
        parent::__construct($faction);
        $this->targetFaction = $targetFaction;
        $this->relation = $relation;
    }

    public function getTargetFaction(): Faction
    {
        return $this->targetFaction;
    }

    public function getRelation(): string
    {
        return $this->relation;
    }
}