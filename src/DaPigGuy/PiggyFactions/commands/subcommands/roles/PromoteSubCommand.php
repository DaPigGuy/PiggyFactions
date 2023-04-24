<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\roles;

use CortexPE\Commando\args\TextArgument;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\role\FactionRoleChangeEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\utils\Roles;
use pocketmine\player\Player;

class PromoteSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $targetMember = $faction->getMember($args["name"]);
        if ($targetMember === null) {
            $member->sendMessage("commands.member-not-found", ["{PLAYER}" => $args["name"]]);
            return;
        }
        $currentRole = $targetMember->getRole();
        if ($currentRole === Roles::OFFICER) {
            $member->sendMessage("commands.promote.already-maxed", ["{PLAYER}" => $targetMember->getUsername()]);
            return;
        }
        if ((Roles::ALL[$currentRole] + 1 >= Roles::ALL[$member->getRole()] && !$member->isInAdminMode()) || $currentRole === Roles::LEADER) {
            $member->sendMessage("commands.promote.cant-promote-higher", ["{PLAYER}" => $targetMember->getUsername()]);
            return;
        }
        $ev = new FactionRoleChangeEvent($faction, $targetMember, $member, $currentRole, ($role = array_keys(Roles::ALL)[Roles::ALL[$currentRole]]));
        $ev->call();
        if ($ev->isCancelled()) return;
        $targetMember->setRole($role);
        $member->sendMessage("commands.promote.success", ["{PLAYER}" => $targetMember->getUsername(), "{ROLE}" => $role]);
        $targetMember->sendMessage("commands.promote.promoted", ["{ROLE}" => $role]);
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("name"));
    }
}