<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\relations;

use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\relation\FactionRelationEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\factions\FactionsManager;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class EnemySubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $targetFaction = FactionsManager::getInstance()->getFactionByName($args["faction"]);
        if ($targetFaction === null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.invalid-faction", ["{FACTION}" => $args["faction"]]);
            return;
        }
        if ($targetFaction->getId() === $faction->getId()) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.enemy.self");
            return;
        }
        if ($faction->isEnemy($targetFaction)) {
            LanguageManager::getInstance()->sendMessage($sender, "already-enemy", ["{FACTION}" => $faction->getName()]);
            return;
        }
        $ev = new FactionRelationEvent([$faction, $targetFaction], Faction::RELATION_ENEMY);
        $ev->call();
        if ($ev->isCancelled()) return;

        $faction->setRelation($targetFaction, Faction::RELATION_ENEMY);
        foreach ($faction->getOnlineMembers() as $p) {
            LanguageManager::getInstance()->sendMessage($p, "commands.enemy.success", ["{FACTION}" => $faction->getName()]);
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