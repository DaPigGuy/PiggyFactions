<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\management;

use DaPigGuy\PiggyFactions\event\FactionMemberEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\event\Cancellable;

class FactionMOTDChangeEvent extends FactionMemberEvent implements Cancellable
{
    private string $motd;

    public function __construct(Faction $faction, FactionsPlayer $member, string $motd)
    {
        parent::__construct($faction, $member);
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