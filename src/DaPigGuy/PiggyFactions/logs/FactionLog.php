<?php

namespace DaPigGuy\PiggyFactions\logs;

use JsonSerializable;

class FactionLog implements JsonSerializable
{
    const BAN = "ban";
    const CLAIM = "claim";
    const DESCRIPTION = "description";
    const INVITE = "invite";
    const KICK = "kick";
    const MOTD = "motd";
    const NAME = "name";
    const JOIN = "join";
    const LEAVE = "leave";

    /** @var string */
    private $name;
    /** @var array */
    private $data;

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