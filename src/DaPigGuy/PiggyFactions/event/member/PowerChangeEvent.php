<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\member;

use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\event\Cancellable;
use pocketmine\event\Event;

class PowerChangeEvent extends Event implements Cancellable
{
    const CAUSE_TIME = 0;
    const CAUSE_DEATH = 1;
    const CAUSE_ADMIN = 2;

    /** @var FactionsPlayer */
    private $member;
    /** @var int */
    private $cause;
    /** @var float */
    private $power;

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