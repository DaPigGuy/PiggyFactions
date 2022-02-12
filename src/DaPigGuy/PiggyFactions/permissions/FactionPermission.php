<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\permissions;

use DaPigGuy\PiggyFactions\utils\Roles;
use JsonSerializable;

class FactionPermission implements JsonSerializable
{
    const ALLY = "ally";
    const BAN = "ban";
    const BUILD = "build";
    const CLAIM = "claim";
    const CONTAINERS = "containers";
    const DEMOTE = "demote";
    const DESCRIPTION = "description";
    const ENEMY = "enemy";
    const FLAG = "flag";
    const FLY = "fly";
    const INVITE = "invite";
    const INTERACT = "interact";
    const KICK = "kick";
    const MOTD = "motd";
    const NAME = "name";
    const NEUTRAL = "neutral";
    const PROMOTE = "promote";
    const SETHOME = "sethome";
    const UNALLY = "unally";
    const UNBAN = "unban";
    const UNCLAIM = "unclaim";

    /** @var string */
    private $name;
    /** @var array */
    private $holders;

    public function __construct(string $name, array $holders = [Roles::OFFICER])
    {
        $this->name = $name;
        $this->holders = $holders;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHolders(): array
    {
        return $this->holders;
    }

    public function setHolders(array $holders): void
    {
        $this->holders = $holders;
    }

    public function addHolder(string $holder): void
    {
        if (!in_array($holder, $this->holders)) $this->holders[] = $holder;
    }

    public function removeHolder(string $holder): void
    {
        unset($this->holders[array_search($holder, $this->holders)]);
    }

    public function jsonSerialize(): array
    {
        return array_values($this->holders);
    }
}