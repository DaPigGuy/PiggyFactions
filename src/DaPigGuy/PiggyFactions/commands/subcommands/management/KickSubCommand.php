<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\management;

use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\management\FactionKickEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class KickSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $target = $faction->getMember($args["name"]);
        if ($target === null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.member-not-found", ["{PLAYER}" => $args["name"]]);
            return;
        }
        if (Faction::ROLES[$target->getRole()] >= Faction::ROLES[$member->getRole()] && !$member->isInAdminMode()) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.kick.cant-kick-higher", ["{PLAYER}" => $target->getUsername()]);
            return;
        }
        $ev = new FactionKickEvent($faction, $target, $member);
        $ev->call();
        if ($ev->isCancelled()) return;

        $faction->removeMember($target->getUuid());
        foreach ($faction->getOnlineMembers() as $online) {
            LanguageManager::getInstance()->sendMessage($online, "commands.kick.announcement", ["{PLAYER}" => $target->getUsername()]);
        }
        if (($p = $this->plugin->getServer()->getPlayerByUUID($target->getUuid())) instanceof Player) {
            LanguageManager::getInstance()->sendMessage($p, "commands.kick.kicked");
        }
    }

    /**
     * @throws ArgumentOrderException
     */
    public function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("name"));
    }
}