<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\role;

use DaPigGuy\PiggyFactions\event\FactionMemberEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\event\Cancellable;

class FactionRoleChangeEvent extends FactionMemberEvent implements Cancellable
{
    public function __construct(Faction $faction, FactionsPlayer $member, private FactionsPlayer $changedBy, private ?string $oldRole, private ?string $newRole)
    {
        parent::__construct($faction, $member);
    }

    public function getChangedBy(): FactionsPlayer
    {
        return $this->changedBy;
    }

    public function getOldRole(): ?string
    {
        return $this->oldRole;
    }

    public function getNewRole(): ?string
    {
        return $this->newRole;
    }
}