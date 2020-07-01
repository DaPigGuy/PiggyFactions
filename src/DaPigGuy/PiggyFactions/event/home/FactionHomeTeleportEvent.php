<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\home;

use DaPigGuy\PiggyFactions\event\FactionEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use pocketmine\event\Cancellable;
use pocketmine\player\Player;

class FactionHomeTeleportEvent extends FactionEvent implements Cancellable
{
    /** @var Player */
    private $player;

    public function __construct(Faction $faction, Player $player)
    {
        parent::__construct($faction);
        $this->player = $player;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }
}