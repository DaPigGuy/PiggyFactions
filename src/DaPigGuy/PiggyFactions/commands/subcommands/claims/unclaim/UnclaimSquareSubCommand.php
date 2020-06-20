<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\claims\unclaim;

use CortexPE\Commando\args\IntegerArgument;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use pocketmine\level\Position;
use pocketmine\Player;

class UnclaimSquareSubCommand extends UnclaimMultipleSubCommand
{
    public function getPositions(Player $player, array $args): array
    {
        if (($radius = (int)$args["radius"]) < 1) {
            LanguageManager::getInstance()->sendMessage($player, "commands.claim.radius-less-than-one");
            return [];
        }
        $radius--;

        $center = $player->getLevel()->getChunkAtPosition($player);
        $positions = [];
        for ($dx = -$radius; $dx <= $radius; $dx++) {
            for ($dz = -$radius; $dz <= $radius; $dz++) {
                $positions[] = new Position(($center->getX() + $dx) << 4, 0, ($center->getZ() + $dz) << 4, $player->getLevel());
            }
        }
        return $positions;
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new IntegerArgument("radius"));
    }
}