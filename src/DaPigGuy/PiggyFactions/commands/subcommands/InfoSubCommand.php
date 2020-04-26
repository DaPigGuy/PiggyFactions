<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\factions\FactionsManager;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class InfoSubCommand extends FactionSubCommand
{
    /** @var bool */
    public $requiresFaction = false;

    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        if (isset($args["faction"])) {
            $faction = FactionsManager::getInstance()->getFactionByName($args["faction"]);
            if ($faction === null) {
                LanguageManager::getInstance()->sendMessage($sender, "commands.invalid-faction", ["{FACTION}" => $args["faction"]]);
                return;
            }
        }
        if ($faction === null) {
            $this->sendUsage();
            return;
        }

        $memberNamesWithRole = [];
        $memberNamesByRole = [];
        foreach ($faction->getMembers() as $m) {
            $memberNamesByRole[$m->getRole()][] = $m->getUsername();
            if (!$m->getUuid()->equals($faction->getLeader())) $memberNamesWithRole[] = $this->plugin->getTagManager()->getPlayerRankSymbol($m) . $m->getUsername();
        }

        LanguageManager::getInstance()->sendMessage($sender, "commands.info.message", [
            "{FACTION}" => $faction->getName(),
            "{DESCRIPTION}" => $faction->getDescription(),
            "{POWER}" => round($faction->getPower(), 2, PHP_ROUND_HALF_DOWN),
            "{TOTALPOWER}" => count($faction->getMembers()) * $this->plugin->getConfig()->getNested("factions.power.max"),
            "{LEADER}" => $faction->getMemberByUUID($faction->getLeader())->getUsername(),
            "{ALLIES}" => implode(",", array_map(function (Faction $f): string {
                return $f->getName();
            }, $faction->getAllies())),
            "{ENEMIES}" => implode(",", array_map(function (Faction $f): string {
                return $f->getName();
            }, $faction->getEnemies())),
            "{OFFICERS}" => implode(",", $memberNamesByRole[Faction::ROLE_OFFICER] ?? []),
            "{MEMBERS}" => implode(",", $memberNamesByRole[Faction::ROLE_MEMBER] ?? []),
            "{RECRUITS}" => implode(",", $memberNamesByRole[Faction::ROLE_RECRUIT] ?? []),
            "{PLAYERS}" => implode(",", $memberNamesWithRole),
            "{TOTALPLAYERS}" => count($faction->getMembers()),
            "{ONLINECOUNT}" => count($faction->getOnlineMembers())
        ]);
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("faction", true));
    }
}