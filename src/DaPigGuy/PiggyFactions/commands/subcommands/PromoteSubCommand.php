<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use pocketmine\Player;

class PromoteSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, string $aliasUsed, array $args): void
    {
        $member = $faction->getMember($args["name"]);
        if ($member === null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.member-not-found", ["{PLAYER}" => $args["name"]]);
            return;
        }
        $currentRole = $member->getRole();
        if ($currentRole === Faction::ROLE_OFFICER) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.promote.already-maxed", ["{PLAYER}" => $member->getUsername()]);
            return;
        }
        if (Faction::ROLES[$currentRole] + 1 >= Faction::ROLES[$faction->getMember($sender->getName())->getRole()]) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.promote.cant-promote-higher", ["{PLAYER}" => $member->getUsername()]);
            return;
        }
        $member->setRole(($role = array_keys(Faction::ROLES)[Faction::ROLES[$currentRole]]));
        LanguageManager::getInstance()->sendMessage($sender, "commands.promote.success", ["{PLAYER}" => $member->getUsername(), "{ROLE}" => $role]);
        if (($player = $this->plugin->getServer()->getPlayerByUUID($member->getUuid())) !== null) LanguageManager::getInstance()->sendMessage($player, "commands.promote.promoted", ["{ROLE}" => $role]);
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("name"));
    }
}