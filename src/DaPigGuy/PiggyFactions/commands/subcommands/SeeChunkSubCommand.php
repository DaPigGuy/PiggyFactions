<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\PlayerManager;
use pocketmine\Player;

class SeeChunkSubCommand extends FactionSubCommand
{
    /** @var bool */
    protected $requiresFaction = false;

    public function onNormalRun(Player $sender, ?Faction $faction, string $aliasUsed, array $args): void
    {
        PlayerManager::getInstance()->getPlayer($sender->getUniqueId())->setCanSeeChunks(!PlayerManager::getInstance()->getPlayer($sender->getUniqueId())->canSeeChunks());
    }
}