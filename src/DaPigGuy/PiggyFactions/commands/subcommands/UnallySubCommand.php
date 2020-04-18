<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\factions\FactionsManager;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class UnallySubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $targetFaction = FactionsManager::getInstance()->getFactionByName($args["faction"]);
        if ($targetFaction === null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.invalid-faction", ["{FACTION}" => $args["faction"]]);
            return;
        }
        if ($targetFaction->getRelation($faction) !== Faction::RELATION_ALLY) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.unally.not-allied", ["{FACTION}" => $targetFaction->getName()]);
            return;
        }
        $faction->setRelation($targetFaction, Faction::RELATION_NONE);
        $targetFaction->setRelation($faction, Faction::RELATION_NONE);
        foreach ($faction->getOnlineMembers() as $p) {
            LanguageManager::getInstance()->sendMessage($p, "commands.unally.unallied", ["{FACTION}" => $targetFaction->getName()]);
        }
        foreach ($targetFaction->getOnlineMembers() as $p) {
            LanguageManager::getInstance()->sendMessage($p, "commands.unally.unallied", ["{FACTION}" => $faction->getName()]);
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