<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\claims\claim;

use CortexPE\Commando\args\IntegerArgument;
use pocketmine\player\Player;
use pocketmine\world\format\Chunk;

class ClaimSquareSubCommand extends ClaimMultipleSubCommand
{
    public function getChunks(Player $player, array $args): array
    {
        if (($radius = (int)$args["radius"]) < 1) {
            $this->plugin->getLanguageManager()->sendMessage($player, "commands.claim.radius-less-than-one");
            return [];
        }
        $radius--;

        $centerX = $player->getPosition()->getFloorX() >> Chunk::COORD_BIT_SIZE;
        $centerZ = $player->getPosition()->getFloorZ() >> Chunk::COORD_BIT_SIZE;
        $chunks = [];
        for ($dx = -$radius; $dx <= $radius; $dx++) {
            for ($dz = -$radius; $dz <= $radius; $dz++) {
                $chunks[] = [$centerX + $dx, $centerZ + $dz];
            }
        }
        return $chunks;
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new IntegerArgument("radius"));
    }
}