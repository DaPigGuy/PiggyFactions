<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\flags;

use DaPigGuy\PiggyFactions\event\FactionEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use pocketmine\event\Cancellable;

class FactionFlagChangeEvent extends FactionEvent implements Cancellable
{
    /** @var string */
    private $flag;
    /** @var bool */
    private $value;

    public function __construct(Faction $faction, string $flag, bool $value)
    {
        parent::__construct($faction);
        $this->flag = $flag;
        $this->value = $value;
    }

    public function getFlag(): string
    {
        return $this->flag;
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