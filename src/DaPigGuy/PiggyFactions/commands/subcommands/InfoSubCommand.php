<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class InfoSubCommand extends FactionSubCommand
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
        LanguageManager::getInstance()->sendMessage($sender, "commands.info.message", [
            "{DESCRIPTION}" => $faction->getDescription(),
            "{POWER}" => $faction->getPower(),
            "{LEADER}" => $faction->getMemberByUUID($faction->getLeader())->getUsername(),
            "{OFFICERS}" => implode(", ", array_map(function (FactionsPlayer $member): string {
                return $member->getUsername();
            }, array_filter($faction->getMembers(), function (FactionsPlayer $member): bool {
                return $member->getRole() === Faction::ROLE_OFFICER;
            }))),
            "{MEMBERS}" => implode(",", array_map(function (FactionsPlayer $member): string {
                return $member->getUsername();
            }, array_filter($faction->getMembers(), function (FactionsPlayer $member): bool {
                return $member->getRole() === Faction::ROLE_MEMBER;
            }))),
            "{ONLINECOUNT}" => count($faction->getOnlineMembers())
        ]);
    }
}