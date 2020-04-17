<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use pocketmine\Player;

class InviteSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, string $aliasUsed, array $args): void
    {
        $target = $this->plugin->getServer()->getPlayer($args["name"]);
        if (!$target instanceof Player) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.invalid-player", ["{PLAYER}" => $args["name"]]);
            return;
        }
        $targetFaction = $this->plugin->getPlayerManager()->getPlayerFaction($target->getUniqueId());
        if (!$faction->hasPermission($faction->getMember($sender->getName()), $this->getName())) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.no-permission");
            return;
        }
        if ($targetFaction !== null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.invite.already-in-faction", ["{PLAYER}" => $target->getName()]);
            return;
        }
        if ($faction->hasInvite($target)) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.invite.already-sent", ["{PLAYER}" => $target->getName()]);
            return;
        }
        $faction->invitePlayer($target);
        LanguageManager::getInstance()->sendMessage($sender, "commands.invite.success", ["{PLAYER}" => $target->getName()]);
        LanguageManager::getInstance()->sendMessage($target, "commands.invite.invited", ["{FACTION}" => $faction->getName()]);
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("name"));
    }
}