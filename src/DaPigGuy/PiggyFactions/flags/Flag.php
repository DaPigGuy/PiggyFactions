<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\flags;

use JsonSerializable;

class Flag implements JsonSerializable
{
    const OPEN = "open";

    const WARZONE = "warzone";
    const SAFEZONE = "safezone";

    /** @var string */
    private $name;
    /** @var bool */
    private $editable;
    /** @var bool */
    private $value;

    public function __construct(string $name, bool $editable, bool $value)
    {
        $this->name = $name;
        $this->editable = $editable;
        $this->value = $value;
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