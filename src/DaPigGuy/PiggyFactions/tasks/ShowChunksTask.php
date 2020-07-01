<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\tasks;

use DaPigGuy\PiggyFactions\PiggyFactions;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\world\particle\RedstoneParticle;

class ShowChunksTask extends Task
{
    /** @var PiggyFactions */
    private $plugin;

    public function __construct(PiggyFactions $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onRun(): void
    {
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $p) {
            if (($member = $this->plugin->getPlayerManager()->getPlayer($p)) !== null && $member->canSeeChunks()) {
                $chunk = $p->getWorld()->getChunkAtPosition($p->getPosition());

                $minX = (float)$chunk->getX() * 16;
                $maxX = (float)$minX + 16;
                $minZ = (float)$chunk->getZ() * 16;
                $maxZ = (float)$minZ + 16;

                for ($x = $minX; $x <= $maxX; $x += 0.5) {
                    for ($z = $minZ; $z <= $maxZ; $z += 0.5) {
                        if ($x === $minX || $x === $maxX || $z === $minZ || $z === $maxZ) {
                            $p->getWorld()->addParticle(new Vector3($x, $p->getPosition()->y + 1.5, $z), new RedstoneParticle(), [$p]);
                        }
                    }
                }
            }
        }
    }
}