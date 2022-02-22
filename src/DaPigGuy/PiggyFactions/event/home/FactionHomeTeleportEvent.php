<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\home;

use DaPigGuy\PiggyFactions\event\FactionEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use pocketmine\event\Cancellable;
use pocketmine\player\Player;

class FactionHomeTeleportEvent extends FactionEvent implements Cancellable
{
    public function __construct(Faction $faction, private Player $player)
    {
        parent::__construct($faction);
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }
}