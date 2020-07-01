<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event;

use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\event\CancellableTrait;

abstract class FactionMemberEvent extends FactionEvent
{
    use CancellableTrait;

    /** @var FactionsPlayer */
    private $member;

    public function __construct(Faction $faction, FactionsPlayer $member)
    {
        parent::__construct($faction);
        $this->member = $member;
    }

    public function getMember(): FactionsPlayer
    {
        return $this->member;
    }
}