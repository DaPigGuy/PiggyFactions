<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\management;

use CortexPE\Commando\args\TextArgument;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\management\FactionKickEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\utils\Roles;
use pocketmine\player\Player;

class KickSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $target = $faction->getMember($args["name"]);
        if ($target === null) {
            $member->sendMessage("commands.member-not-found", ["{PLAYER}" => $args["name"]]);
            return;
        }
        if ($target->getRole() == null || $member->getRole() == null) {
            $member->sendMessage("generic-error", ["{CONTEXT}" => "Unable to obtain role data."]);
            return;
        }
        if (Roles::ALL[$target->getRole()] >= Roles::ALL[$member->getRole()] && !$member->isInAdminMode()) {
            $member->sendMessage("commands.kick.cant-kick-higher", ["{PLAYER}" => $target->getUsername()]);
            return;
        }
        $ev = new FactionKickEvent($faction, $target, $member);
        $ev->call();
        if ($ev->isCancelled()) return;

        $faction->removeMember($target->getUuid());
        $faction->broadcastMessage("commands.kick.announcement", ["{PLAYER}" => $target->getUsername()]);
        $target->sendMessage("commands.kick.kicked");
    }

    public function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("name"));
    }
}