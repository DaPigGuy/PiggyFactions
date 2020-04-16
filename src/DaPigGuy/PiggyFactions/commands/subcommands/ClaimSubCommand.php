<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use DaPigGuy\PiggyFactions\claims\ClaimsManager;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ClaimSubCommand extends FactionSubCommand
{
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "Please use this command in-game.");
            return;
        }
        $faction = $this->plugin->getPlayerManager()->getPlayerFaction($sender->getUniqueId());
        if ($faction === null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.not-in-faction");
            return;
        }
        if (!$faction->hasPermission($faction->getMember($sender->getName()), $this->getName())) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.no-permission");
            return;
        }
        $claim = ClaimsManager::getInstance()->getClaim($sender->getLevel(), $sender->getLevel()->getChunkAtPosition($sender));
        if ($claim !== null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.claim.already-claimed");
            return;
        }
        if ($faction->getPower() / $this->plugin->getConfig()->getNested("factions.claim.cost", 1) < count(ClaimsManager::getInstance()->getFactionClaims($faction)) - 1) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.claim.no-power");
            return;
        }
        ClaimsManager::getInstance()->createClaim($faction, $sender->getLevel(), $sender->getLevel()->getChunkAtPosition($sender));
        LanguageManager::getInstance()->sendMessage($sender, "commands.claim.success");
    }
}