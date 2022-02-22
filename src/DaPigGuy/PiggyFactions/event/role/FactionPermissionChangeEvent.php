<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\role;

use DaPigGuy\PiggyFactions\event\FactionEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use pocketmine\event\Cancellable;

class FactionPermissionChangeEvent extends FactionEvent implements Cancellable
{
    public function __construct(Faction $faction, private string $role, private string $permission, private bool $value)
    {
        parent::__construct($faction);
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getPermission(): string
    {
        return $this->permission;
    }

    public function getValue(): bool
    {
        return $this->value;
    }

    public function setValue(bool $value): void
    {
        $this->value = $value;
    }
}