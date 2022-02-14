<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\tasks;

use DaPigGuy\PiggyFactions\event\member\PowerChangeEvent;
use DaPigGuy\PiggyFactions\PiggyFactions;
use pocketmine\scheduler\Task;

class UpdatePowerTask extends Task
{
    const INTERVAL = 5 * 60 * 20;

    private PiggyFactions $plugin;

    public function __construct(PiggyFactions $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onRun(): void
    {
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $p) {
            $member = $this->plugin->getPlayerManager()->getPlayer($p);
            if ($member !== null) {
                $ev = new PowerChangeEvent($member, PowerChangeEvent::CAUSE_TIME, $member->getPower() + $this->plugin->getConfig()->getNested("factions.power.per.hour", 2) / (72000 / self::INTERVAL));
                $ev->call();
                if ($ev->isCancelled()) return;
                $member->setPower($ev->getPower());
            }
        }
    }
}