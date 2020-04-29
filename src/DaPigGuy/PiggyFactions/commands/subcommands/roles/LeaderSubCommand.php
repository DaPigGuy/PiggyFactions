<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\roles;

use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\role\FactionLeadershipTransferEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\utils\Roles;
use pocketmine\Player;

class LeaderSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        if ($member->getRole() !== Roles::LEADER && !$member->isInAdminMode()) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.not-leader");
            return;
        }
        $targetMember = $faction->getMember($args["name"]);
        if ($targetMember === null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.member-not-found", ["{PLAYER}" => $args["name"]]);
            return;
        }

        $ev = new FactionLeadershipTransferEvent($faction, $member, $targetMember);
        $ev->call();
        if ($ev->isCancelled()) return;

        if ($leader = $faction->getLeader() !== null) $leader->setRole(Roles::MEMBER);
        $targetMember->setRole(Roles::LEADER);
        if (($player = $this->plugin->getServer()->getPlayerByUUID($targetMember->getUuid())) instanceof Player) LanguageManager::getInstance()->sendMessage($player, "commands.leader.recipient");
        LanguageManager::getInstance()->sendMessage($sender, "commands.leader.success", ["{PLAYER}" => $targetMember->getUsername()]);
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("name"));
    }
}