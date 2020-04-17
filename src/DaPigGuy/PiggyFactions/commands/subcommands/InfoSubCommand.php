<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class InfoSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, string $aliasUsed, array $args): void
    {
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