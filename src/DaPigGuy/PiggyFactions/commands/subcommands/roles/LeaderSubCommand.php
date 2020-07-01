<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\roles;

use CortexPE\Commando\args\TextArgument;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\role\FactionLeadershipTransferEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\utils\Roles;
use pocketmine\player\Player;

class LeaderSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        if ($member->getRole() !== Roles::LEADER && !$member->isInAdminMode()) {
            $member->sendMessage("commands.not-leader");
            return;
        }
        $targetMember = $faction->getMember($args["name"]);
        if ($targetMember === null) {
            $member->sendMessage("commands.member-not-found", ["{PLAYER}" => $args["name"]]);
            return;
        }

        $ev = new FactionLeadershipTransferEvent($faction, $member, $targetMember);
        $ev->call();
        if ($ev->isCancelled()) return;

        if (($leader = $faction->getLeader()) !== null) $leader->setRole(Roles::MEMBER);
        $targetMember->setRole(Roles::LEADER);
        $targetMember->sendMessage("commands.leader.recipient");
        $member->sendMessage("commands.leader.success", ["{PLAYER}" => $targetMember->getUsername()]);
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("name"));
    }
}