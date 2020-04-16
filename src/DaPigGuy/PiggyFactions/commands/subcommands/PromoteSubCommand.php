<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use CortexPE\Commando\args\TextArgument;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class PromoteSubCommand extends FactionSubCommand
{

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "Please use this command in-game.");
            return;
        }
        $faction = $this->plugin->getPlayerManager()->getPlayerFaction($sender->getUniqueId());
        if ($faction === null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.not-in-faction");
            return;
        }
        if ($faction->getMember($sender->getName())->getRole() !== Faction::ROLE_LEADER) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.not-leader");
            return;
        }
        $member = $faction->getMember($args["name"]);
        if ($member === null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.member-not-found", ["{PLAYER}" => $args["name"]]);
            return;
        }
        $currentRole = $member->getRole();
        if (Faction::ROLES[$currentRole] === count(Faction::ROLES) - 1) {
            //TODO: maxed msg
            return;
        }
        $member->setRole(array_keys(Faction::ROLES)[Faction::ROLES[$currentRole]]);
        //TODO: promoted msg
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("name"));
    }
}