<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\management;

use DaPigGuy\PiggyFactions\event\FactionMemberEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\logs\FactionLog;
use DaPigGuy\PiggyFactions\logs\LogsManager;
use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\event\Cancellable;

class FactionKickEvent extends FactionMemberEvent implements Cancellable
{
    /** @var FactionsPlayer */
    private $kickedBy;

    public function __construct(Faction $faction, FactionsPlayer $member, FactionsPlayer $kickedBy)
    {
        parent::__construct($faction, $member);
        $this->kickedBy = $kickedBy;
    }

    public function getKickedBy(): FactionsPlayer
    {
        return $this->kickedBy;
    }
}