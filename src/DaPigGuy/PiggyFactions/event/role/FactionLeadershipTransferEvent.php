<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\role;

use DaPigGuy\PiggyFactions\event\FactionEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\event\Cancellable;

class FactionLeadershipTransferEvent extends FactionEvent implements Cancellable
{
    public function __construct(Faction $faction, private FactionsPlayer $old, private FactionsPlayer $new)
    {
        parent::__construct($faction);
    }

    public function getOld(): FactionsPlayer
    {
        return $this->old;
    }

    public function getNew(): FactionsPlayer
    {
        return $this->new;
    }
}