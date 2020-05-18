<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\flags;

use DaPigGuy\PiggyFactions\event\FactionMemberEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\event\Cancellable;

class FactionFlagChangeEvent extends FactionMemberEvent implements Cancellable
{
    /** @var string */
    private $flag;
    /** @var bool */
    private $value;

    public function __construct(Faction $faction, FactionsPlayer $member, string $flag, bool $value)
    {
        parent::__construct($faction, $member);
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