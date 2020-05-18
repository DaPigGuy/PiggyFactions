<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\role;

use DaPigGuy\PiggyFactions\event\FactionMemberEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\event\Cancellable;

class FactionRoleChangeEvent extends FactionMemberEvent implements Cancellable
{
    /** @var FactionsPlayer */
    private $changedBy;

    /** @var string|null */
    private $oldRole;
    /** @var string|null */
    private $newRole;

    public function __construct(Faction $faction, FactionsPlayer $member, FactionsPlayer $changedBy, ?string $oldRole, ?string $newRole)
    {
        parent::__construct($faction, $member);
        $this->changedBy = $changedBy;
        $this->oldRole = $oldRole;
        $this->newRole = $newRole;
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