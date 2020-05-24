<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use CortexPE\Commando\args\TextArgument;
use DaPigGuy\PiggyFactions\event\member\FactionJoinEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\flags\Flag;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class JoinSubCommand extends FactionSubCommand
{
    /** @var bool */
    protected $requiresFaction = false;

    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $targetFaction = $this->plugin->getFactionsManager()->getFactionByName($args["faction"]);
        if ($targetFaction === null) {
            $member->sendMessage("commands.invalid-faction", ["{FACTION}" => $args["faction"]]);
            return;
        }
        if (!$targetFaction->hasInvite($sender) && !$targetFaction->getFlag(Flag::OPEN) && !$member->isInAdminMode()) {
            $member->sendMessage("commands.join.no-invite", ["{FACTION}" => $targetFaction->getName()]);
            return;
        }
        if ($faction !== null) {
            $member->sendMessage("commands.already-in-faction");
            return;
        }
        if (!$member->isInAdminMode()) {
            if ($targetFaction->isBanned($sender->getUniqueId())) {
                $member->sendMessage("commands.you-are-banned");
                return;
            }
            if (count($targetFaction->getMembers()) >= ($maxPlayers = $this->plugin->getConfig()->getNested("factions.max-players", -1)) && $maxPlayers !== -1) {
                $member->sendMessage("commands.faction-full");
                return;
            }
        }
        $ev = new FactionJoinEvent($targetFaction, $member);
        $ev->call();
        if ($ev->isCancelled()) return;

        $targetFaction->revokeInvite($sender);
        $targetFaction->addMember($sender);
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("faction"));
    }

}