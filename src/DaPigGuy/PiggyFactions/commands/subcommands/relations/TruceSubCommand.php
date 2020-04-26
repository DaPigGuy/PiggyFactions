<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\relations;

use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\relation\FactionRelationEvent;
use DaPigGuy\PiggyFactions\event\relation\FactionRelationWishEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\factions\FactionsManager;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class TruceSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $targetFaction = FactionsManager::getInstance()->getFactionByName($args["faction"]);
        if ($targetFaction === null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.invalid-faction", ["{FACTION}" => $args["faction"]]);
            return;
        }
        if ($targetFaction->getId() === $faction->getId()) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.truce.self");
            return;
        }
        if ($targetFaction->isTruced($faction)) {
            LanguageManager::getInstance()->sendMessage($sender, "already-truced");
            return;
        }
        if ($targetFaction->getRelationWish($faction) === Faction::RELATION_TRUCE) {
            $ev = new FactionRelationEvent([$faction, $targetFaction], Faction::RELATION_TRUCE);
            $ev->call();
            if ($ev->isCancelled()) return;

            $targetFaction->revokeRelationWish($faction);
            $faction->setRelation($targetFaction, Faction::RELATION_TRUCE);
            $targetFaction->setRelation($faction, Faction::RELATION_TRUCE);
            foreach ($faction->getOnlineMembers() as $p) {
                LanguageManager::getInstance()->sendMessage($p, "commands.truce.truced", ["{TRUCED}" => $targetFaction->getName()]);
            }
            foreach ($targetFaction->getOnlineMembers() as $p) {
                LanguageManager::getInstance()->sendMessage($p, "commands.truce.truced", ["{TRUCED}" => $faction->getName()]);
            }
            return;
        }
        $ev = new FactionRelationWishEvent($faction, $targetFaction, Faction::RELATION_TRUCE);
        $ev->call();
        if ($ev->isCancelled()) return;

        $faction->setRelationWish($targetFaction, Faction::RELATION_TRUCE);
        LanguageManager::getInstance()->sendMessage($sender, "commands.truce.success", ["{FACTION}" => $targetFaction->getName()]);
        foreach ($targetFaction->getOnlineMembers() as $p) {
            LanguageManager::getInstance()->sendMessage($p, "commands.truce.request", ["{FACTION}" => $faction->getName()]);
        }
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("faction"));
    }
}