<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\flags;

use JsonSerializable;

class Flag implements JsonSerializable
{
    const OPEN = "open";

    const WARZONE = "warzone";
    const SAFEZONE = "safezone";

    public function __construct(private string $name, private bool $editable, private bool $value)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isEditable(): bool
    {
        return $this->editable;
    }

    public function getValue(): bool
    {
        return $this->value;
    }

    public function setValue(bool $value): void
    {
        $this->value = $value;
    }

    public function jsonSerialize(): bool
    {
        return $this->value;
    }
}