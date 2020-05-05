<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\management;

use CortexPE\Commando\args\RawStringArgument;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\management\FactionBanEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\players\PlayerManager;
use DaPigGuy\PiggyFactions\utils\Roles;
use pocketmine\Player;

class BanSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $target = PlayerManager::getInstance()->getPlayerByName($args["name"]);
        if ($target === null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.invalid-player", ["{PLAYER}" => $args["name"]]);
            return;
        }
        if (Roles::ALL[$target->getRole()] >= Roles::ALL[$member->getRole()] && $target->getRole() !== Roles::LEADER && !$member->isInAdminMode()) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.ban.cant-ban-higher", ["{PLAYER}" => $target->getUsername()]);
            return;
        }
        if ($faction->isBanned($target->getUuid())) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.ban.already-banned", ["{PLAYER}" => $target->getUsername()]);
            return;
        }
        $ev = new FactionBanEvent($faction, $target, $member);
        $ev->call();
        if ($ev->isCancelled()) return;

        $faction->banPlayer($target->getUuid());
        foreach ($faction->getOnlineMembers() as $online) {
            LanguageManager::getInstance()->sendMessage($online, "commands.ban.announcement", ["{PLAYER}" => $target->getUsername()]);
        }
        if ($faction->getId() === $target->getFaction()->getId()) {
            $faction->removeMember($target->getUuid());
            if (($p = $this->plugin->getServer()->getPlayerByUUID($target->getUuid())) instanceof Player) {
                LanguageManager::getInstance()->sendMessage($p, "commands.ban.banned");
            }
        }
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("name"));
    }
}