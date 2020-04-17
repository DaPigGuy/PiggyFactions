<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use pocketmine\Player;

class KickSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, string $aliasUsed, array $args): void
    {
        $target = $faction->getMember($args["name"]);
        if ($target === null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.member-not-found", ["{PLAYER}" => $args["name"]]);
            return;
        }
        if (!$faction->hasPermission($faction->getMember($sender->getName()), $this->getName())) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.no-permission");
            return;
        }
        if ($target->getRole() === Faction::ROLE_LEADER) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.kick.cant-kick-leader");
            return;
        }
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