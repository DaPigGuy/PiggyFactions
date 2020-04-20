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

        $memberNamesByRole = [];
        foreach ($faction->getMembers() as $m) {
            $memberNamesByRole[$m->getRole()][] = $m->getUsername();
        }

        LanguageManager::getInstance()->sendMessage($sender, "commands.info.message", [
            "{FACTION}" => $faction->getName(),
            "{DESCRIPTION}" => $faction->getDescription(),
            "{POWER}" => round($faction->getPower(), 2, PHP_ROUND_HALF_DOWN),
            "{LEADER}" => $faction->getMemberByUUID($faction->getLeader())->getUsername(),
            "{OFFICERS}" => implode(",", $memberNamesByRole[Faction::ROLE_OFFICER] ?? []),
            "{MEMBERS}" => implode(",", $memberNamesByRole[Faction::ROLE_MEMBER] ?? []),
            "{RECRUITS}" => implode(",", $memberNamesByRole[Faction::ROLE_RECRUIT] ?? []),
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