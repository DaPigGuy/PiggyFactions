<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use pocketmine\Player;

class DemoteSubCommand extends FactionSubCommand
{

    public function onNormalRun(Player $sender, ?Faction $faction, string $aliasUsed, array $args): void
    {
        $member = $faction->getMember($args["name"]);
        if ($member === null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.member-not-found", ["{PLAYER}" => $args["name"]]);
            return;
        }
        if (Faction::ROLES[$member->getRole()] >= Faction::ROLES[$faction->getMember($sender->getName())->getRole()]) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.demote.cant-demote-higher", ["{PLAYER}" => $member->getUsername()]);
            return;
        }
        $currentRole = $member->getRole();
        if ($currentRole === Faction::ROLE_RECRUIT) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.demote.already-lowest", ["{PLAYER}" => $member->getUsername()]);
            return;
        }
        $member->setRole(($role = array_keys(Faction::ROLES)[Faction::ROLES[$currentRole] - 2]));
        LanguageManager::getInstance()->sendMessage($sender, "commands.demote.success", ["{PLAYER}" => $member->getUsername(), "{ROLE}" => $role]);
        if (($player = $this->plugin->getServer()->getPlayerByUUID($member->getUuid())) !== null) LanguageManager::getInstance()->sendMessage($player, "commands.demote.demoted", ["{ROLE}" => $role]);
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("name"));
    }
}