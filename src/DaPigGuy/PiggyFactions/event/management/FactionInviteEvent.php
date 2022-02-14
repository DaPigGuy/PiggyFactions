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
    private FactionsPlayer $invitedBy;
    private Player $invited;

    public function __construct(Faction $faction, FactionsPlayer $invitedBy, Player $invited)
    {
        parent::__construct($faction);
        $this->invitedBy = $invitedBy;
        $this->invited = $invited;
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