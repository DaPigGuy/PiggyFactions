<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\management;

use DaPigGuy\PiggyFactions\event\FactionEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use pocketmine\event\Cancellable;

class FactionDescriptionChangeEvent extends FactionEvent implements Cancellable
{
    /** @var string */
    private $description;

    public function __construct(Faction $faction, string $description)
    {
        parent::__construct($faction);
        $this->description = $description;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}