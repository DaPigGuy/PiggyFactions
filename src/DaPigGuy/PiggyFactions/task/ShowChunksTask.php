<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\task;

use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\players\PlayerManager;
use pocketmine\level\particle\RedstoneParticle;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;

class ShowChunksTask extends Task
{
    /** @var PiggyFactions */
    private $plugin;

    public function __construct(PiggyFactions $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onRun(int $currentTick): void
    {
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $p) {
            if (($member = PlayerManager::getInstance()->getPlayer($p->getUniqueId())) !== null && $member->canSeeChunks()) {
                $chunk = $p->getLevel()->getChunkAtPosition($p);

                $minX = (float)$chunk->getX() * 16;
                $maxX = (float)$minX + 16;
                $minZ = (float)$chunk->getZ() * 16;
                $maxZ = (float)$minZ + 16;

                for ($x = $minX; $x <= $maxX; $x += 0.5) {
                    for ($z = $minZ; $z <= $maxZ; $z += 0.5) {
                        if ($x === $minX || $x === $maxX || $z === $minZ || $z === $maxZ) {
                            $p->getLevel()->addParticle(new RedstoneParticle(new Vector3($x, $p->y + 1.5, $z)), [$p]);
                        }
                    }
                }
            }
        }
    }
}