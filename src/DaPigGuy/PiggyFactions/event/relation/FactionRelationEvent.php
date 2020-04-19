<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\relation;

use DaPigGuy\PiggyFactions\factions\Faction;
use pocketmine\event\Cancellable;
use pocketmine\event\Event;

class FactionRelationEvent extends Event implements Cancellable
{
    /** @var Faction[] */
    private $factions;
    /** @var string */
    private $relation;

    /**
     * @param Faction[] $factions
     */
    public function __construct(array $factions, string $relation)
    {
        $this->factions = $factions;
        $this->relation = $relation;
    }

    /**
     * @return Faction[]
     */
    public function getFactions(): array
    {
        return $this->factions;
    }

    public function getRelation(): string
    {
        return $this->relation;
    }
}