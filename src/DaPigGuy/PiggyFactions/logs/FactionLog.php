<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\logs;

use JsonSerializable;

class FactionLog implements JsonSerializable
{
    const BAN = "ban";
    const UNBAN = "unban";
    const INVITE = "invite";
    const KICK = "kick";
    const JOIN = "join";
    const LEAVE = "leave";
    const LEADER_CHANGE = "leader_change";

    private string $name;
    private array $data;

    public function __construct(string $name, array $data = [])
    {
        $this->name = $name;
        $this->data = $data;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function jsonSerialize(): array
    {
        return $this->data;
    }
}