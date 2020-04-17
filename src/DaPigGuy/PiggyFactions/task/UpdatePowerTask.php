<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\task;

use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\players\PlayerManager;
use pocketmine\scheduler\Task;

class UpdatePowerTask extends Task
{
    const INTERVAL = 5 * 60 * 20;

    /** @var PiggyFactions */
    private $plugin;

    public function __construct(PiggyFactions $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onRun(int $currentTick)
    {
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $p) {
            $member = PlayerManager::getInstance()->getPlayer($p->getUniqueId());
            $member->setPower($member->getPower() + $this->plugin->getConfig()->getNested("factions.power.per.hour", 2) / (72000 / self::INTERVAL));
        }
    }
}