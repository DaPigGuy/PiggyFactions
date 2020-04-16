<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use DaPigGuy\PiggyFactions\players\PlayerManager;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class SeeChunkSubCommand extends FactionSubCommand
{
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "Please use this command in-game.");
            return;
        }
        PlayerManager::getInstance()->getPlayer($sender->getUniqueId())->setCanSeeChunks(!PlayerManager::getInstance()->getPlayer($sender->getUniqueId())->canSeeChunks());
    }
}