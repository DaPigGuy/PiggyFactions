<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\claims\claim;

use CortexPE\Commando\args\IntegerArgument;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use pocketmine\level\format\Chunk;
use pocketmine\Player;

class ClaimCircleSubCommand extends ClaimMultipleSubCommand
{
    /**
     * @return Chunk[]
     */
    public function getChunks(Player $player, array $args): array
    {
        if (($radius = (int)$args["radius"]) < 1) {
            LanguageManager::getInstance()->sendMessage($player, "commands.claim.radius-less-than-one");
            return [];
        }
        $radius--;

        $center = $player->getLevel()->getChunkAtPosition($player);
        $chunks = [];
        for ($dx = -$radius; $dx <= $radius; $dx++) {
            for ($dz = -$radius; $dz <= $radius; $dz++) {
                if ($dx * $dx + $dz * $dz > $radius * $radius) continue;
                $chunks[] = $player->getLevel()->getChunk($center->getX() + $dx, $center->getZ() + $dz);
            }
        }
        return $chunks;
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new IntegerArgument("radius"));
    }
}