<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\management;

use DaPigGuy\PiggyFactions\libs\CortexPE\Commando\args\RawStringArgument;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\management\FactionBanEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\utils\Roles;
use pocketmine\player\Player;

class BanSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $target = $this->plugin->getPlayerManager()->getPlayerByName($args["name"]);
        if ($target === null) {
            $member->sendMessage("commands.invalid-player", ["{PLAYER}" => $args["name"]]);
            return;
        }
        if ($target->getFaction() !== null && (Roles::ALL[$target->getRole()] >= Roles::ALL[$member->getRole()] && !$member->isInAdminMode())) {
            $member->sendMessage("commands.ban.cant-ban-higher", ["{PLAYER}" => $target->getUsername()]);
            return;
        }
        if ($faction->isBanned($target->getUuid())) {
            $member->sendMessage("commands.ban.already-banned", ["{PLAYER}" => $target->getUsername()]);
            return;
        }
        $ev = new FactionBanEvent($faction, $target, $member);
        $ev->call();
        if ($ev->isCancelled()) return;

        $faction->banPlayer($target->getUuid());
        $faction->broadcastMessage("commands.ban.announcement", ["{PLAYER}" => $target->getUsername()]);
        if ($faction === $target->getFaction()) {
            $faction->removeMember($target->getUuid());
            $target->sendMessage("commands.ban.banned");
        }
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("name"));
    }
}