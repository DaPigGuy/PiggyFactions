<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\event\member\FactionJoinEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\flags\Flag;
use DaPigGuy\PiggyFactions\language\LanguageManager;
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
            LanguageManager::getInstance()->sendMessage($sender, "commands.invalid-faction", ["{FACTION}" => $args["faction"]]);
            return;
        }
        if (!$targetFaction->hasInvite($sender) && !$targetFaction->getFlag(Flag::OPEN) && !$member->isInAdminMode()) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.join.no-invite", ["{FACTION}" => $targetFaction->getName()]);
            return;
        }
        if ($faction !== null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.already-in-faction");
            return;
        }
        if (!$member->isInAdminMode()) {
            if ($targetFaction->isBanned($sender->getUniqueId())) {
                LanguageManager::getInstance()->sendMessage($sender, "commands.you-are-banned");
                return;
            }
            if (count($faction->getMembers()) >= ($maxPlayers = $this->plugin->getConfig()->getNested("factions.max-players", -1)) && $maxPlayers !== -1) {
                LanguageManager::getInstance()->sendMessage($sender, "commands.faction-full");
                return;
            }
        }
        $ev = new FactionJoinEvent($targetFaction, $member);
        $ev->call();
        if ($ev->isCancelled()) return;

        $targetFaction->revokeInvite($sender);
        $targetFaction->addMember($sender);
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("faction"));
    }

}