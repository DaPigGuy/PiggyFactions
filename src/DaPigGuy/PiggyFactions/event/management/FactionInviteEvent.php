<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\management;

use DaPigGuy\PiggyFactions\event\FactionEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\event\Cancellable;
use pocketmine\player\Player;

class FactionInviteEvent extends FactionEvent implements Cancellable
{
    public function __construct(Faction $faction, private FactionsPlayer $invitedBy, private Player $invited)
    {
        parent::__construct($faction);
    }

    public function getInvitedBy(): FactionsPlayer
    {
        return $this->invitedBy;
    }

    public function getInvited(): Player
    {
        return $this->invited;
    }
}