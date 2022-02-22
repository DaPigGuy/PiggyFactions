<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\management;

use DaPigGuy\PiggyFactions\event\FactionMemberEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\event\Cancellable;

class FactionUnbanEvent extends FactionMemberEvent implements Cancellable
{
    public function __construct(Faction $faction, FactionsPlayer $member, private FactionsPlayer $unbannedBy)
    {
        parent::__construct($faction, $member);
    }

    public function getUnbannedBy(): FactionsPlayer
    {
        return $this->unbannedBy;
    }
}