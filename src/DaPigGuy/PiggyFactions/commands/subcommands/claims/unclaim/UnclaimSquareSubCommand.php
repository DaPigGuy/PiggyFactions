<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\claims\unclaim;

use CortexPE\Commando\args\IntegerArgument;
use pocketmine\player\Player;

class UnclaimSquareSubCommand extends UnclaimMultipleSubCommand
{
    public function getChunks(Player $player, array $args): array
    {
        if (($radius = (int)$args["radius"]) < 1) {
            $this->plugin->getLanguageManager()->sendMessage($player, "commands.claim.radius-less-than-one");
            return [];
        }
        $radius--;

        $center = $player->getWorld()->getChunkAtPosition($player);
        $chunks = [];
        for ($dx = -$radius; $dx <= $radius; $dx++) {
            for ($dz = -$radius; $dz <= $radius; $dz++) {
                $chunks[] = [$center->getX() + $dx, $center->getZ() + $dz];
            }
        }
        return $chunks;
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new IntegerArgument("radius"));
    }
}