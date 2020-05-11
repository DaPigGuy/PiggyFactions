<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\management;

use DaPigGuy\PiggyFactions\event\FactionMemberEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\logs\FactionLog;
use DaPigGuy\PiggyFactions\logs\LogsManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\event\Cancellable;

class FactionBanEvent extends FactionMemberEvent implements Cancellable
{
    /** @var FactionsPlayer */
    private $bannedBy;

    public function __construct(Faction $faction, FactionsPlayer $member, FactionsPlayer $bannedBy)
    {
        parent::__construct($faction, $member);
        $this->bannedBy = $bannedBy;
    }

    public function getBannedBy(): FactionsPlayer
    {
        return $this->bannedBy;
    }
}