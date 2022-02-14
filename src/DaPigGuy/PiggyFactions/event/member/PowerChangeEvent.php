<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\member;

use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;

class PowerChangeEvent extends Event implements Cancellable
{
    use CancellableTrait;

    const CAUSE_TIME = 0;
    const CAUSE_DEATH = 1;
    const CAUSE_KILL = 2;
    const CAUSE_ADMIN = 3;

    private FactionsPlayer $member;
    private int $cause;
    private float $power;

    public function __construct(FactionsPlayer $member, int $cause, float $power)
    {
        $this->cause = $cause;
        $this->power = $power;
        $this->member = $member;
    }

    public function getMember(): FactionsPlayer
    {
        return $this->member;
    }

    public function getCause(): int
    {
        return $this->cause;
    }

    public function getPower(): float
    {
        return $this->power;
    }

    public function setPower(float $power): void
    {
        $this->power = $power;
    }
}