<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\management;

use CortexPE\Commando\args\TextArgument;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\management\FactionInviteEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class InviteSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $target = $this->plugin->getServer()->getPlayer($args["name"]);
        if (!$target instanceof Player) {
            $member->sendMessage("commands.invalid-player", ["{PLAYER}" => $args["name"]]);
            return;
        }
        $targetFaction = $this->plugin->getPlayerManager()->getPlayerFaction($target->getUniqueId());
        if ($targetFaction !== null) {
            $member->sendMessage("commands.invite.already-in-faction", ["{PLAYER}" => $target->getName()]);
            return;
        }
        if ($faction->hasInvite($target)) {
            $member->sendMessage("commands.invite.already-sent", ["{PLAYER}" => $target->getName()]);
            return;
        }
        if ($faction->isBanned($target->getUniqueId())) {
            $member->sendMessage("commands.player-is-banned", ["{PLAYER}" => $target->getName()]);
            return;
        }
        if (count($faction->getMembers()) >= ($maxPlayers = $this->plugin->getConfig()->getNested("factions.max-players", -1)) && $maxPlayers !== -1 && !$member->isInAdminMode()) {
            $member->sendMessage("commands.faction-full");
            return;
        }
        $ev = new FactionInviteEvent($faction, $member, $target);
        $ev->call();
        if ($ev->isCancelled()) return;

        $faction->invitePlayer($target);
        $member->sendMessage("commands.invite.success", ["{PLAYER}" => $target->getName()]);
        LanguageManager::getInstance()->sendMessage($target, "commands.invite.invited", ["{FACTION}" => $faction->getName()]);
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("name"));
    }
}