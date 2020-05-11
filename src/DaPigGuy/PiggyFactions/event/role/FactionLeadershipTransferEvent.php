<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\role;

use DaPigGuy\PiggyFactions\event\FactionEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\logs\FactionLog;
use DaPigGuy\PiggyFactions\logs\LogsManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\event\Cancellable;

class FactionLeadershipTransferEvent extends FactionEvent implements Cancellable
{
    /** @var FactionsPlayer */
    private $old;
    /** @var FactionsPlayer */
    private $new;

    public function __construct(Faction $faction, FactionsPlayer $old, FactionsPlayer $new)
    {
        parent::__construct($faction);
        $this->old = $old;
        $this->new = $new;
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