<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\role;

use DaPigGuy\PiggyFactions\event\FactionEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use pocketmine\event\Cancellable;

class FactionPermissionChangeEvent extends FactionEvent implements Cancellable
{
    /** @var string */
    private $role;
    /** @var string */
    private $permission;
    /** @var bool */
    private $value;

    public function __construct(Faction $faction, string $role, string $permission, bool $value)
    {
        parent::__construct($faction);
        $this->role = $role;
        $this->permission = $permission;
        $this->value = $value;
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