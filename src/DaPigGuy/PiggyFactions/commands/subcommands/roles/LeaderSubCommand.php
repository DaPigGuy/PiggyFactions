<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\roles;

use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class LeaderSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        if ($member->getRole() !== Faction::ROLE_LEADER) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.not-leader");
            return;
        }
        $targetMember = $faction->getMember($args["name"]);
        if ($targetMember === null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.member-not-found", ["{PLAYER}" => $args["name"]]);
            return;
        }
        $player = $this->plugin->getServer()->getPlayerByUUID($targetMember->getUuid());
        if ($player === null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.leader.offline");
            return;
        }
        $faction->setLeader($targetMember->getUuid());
        $faction->getMember($sender->getName())->setRole(Faction::ROLE_MEMBER);
        $targetMember->setRole(Faction::ROLE_LEADER);
        LanguageManager::getInstance()->sendMessage($player, "commands.leader.recipient");
        LanguageManager::getInstance()->sendMessage($sender, "commands.leader.success", ["{PLAYER}" => $player->getName()]);
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("name"));
    }
}