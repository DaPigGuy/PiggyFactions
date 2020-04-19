<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\management;

use DaPigGuy\PiggyFactions\event\FactionEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use pocketmine\event\Cancellable;

class FactionMOTDChangeEvent extends FactionEvent implements Cancellable
{
    /** @var string */
    private $motd;

    public function __construct(Faction $faction, string $motd)
    {
        parent::__construct($faction);
        $this->motd = $motd;
    }

    public function getMotd(): string
    {
        return $this->motd;
    }

    public function setMotd(string $motd): void
    {
        $this->motd = $motd;
    }
}