<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\roles;

use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\role\FactionRoleChangeEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\utils\Roles;
use pocketmine\Player;

class PromoteSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $targetMember = $faction->getMember($args["name"]);
        if ($targetMember === null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.member-not-found", ["{PLAYER}" => $args["name"]]);
            return;
        }
        $currentRole = $targetMember->getRole();
        if ($currentRole === Roles::OFFICER) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.promote.already-maxed", ["{PLAYER}" => $targetMember->getUsername()]);
            return;
        }
        if (Roles::ALL[$currentRole] + 1 >= Roles::ALL[$member->getRole()] && !$member->isInAdminMode()) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.promote.cant-promote-higher", ["{PLAYER}" => $targetMember->getUsername()]);
            return;
        }
        $ev = new FactionRoleChangeEvent($faction, $targetMember, $member, $currentRole, ($role = array_keys(Roles::ALL)[Roles::ALL[$currentRole]]));
        $ev->call();
        if ($ev->isCancelled()) return;
        $targetMember->setRole($role);
        LanguageManager::getInstance()->sendMessage($sender, "commands.promote.success", ["{PLAYER}" => $targetMember->getUsername(), "{ROLE}" => $role]);
        if (($player = $this->plugin->getServer()->getPlayerByUUID($targetMember->getUuid())) !== null) LanguageManager::getInstance()->sendMessage($player, "commands.promote.promoted", ["{ROLE}" => $role]);
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("name"));
    }
}