<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use DaPigGuy\PiggyFactions\libs\CortexPE\Commando\args\TextArgument;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\utils\FormattedTime;
use DaPigGuy\PiggyFactions\utils\Roles;
use DaPigGuy\PiggyFactions\utils\RoundValue;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class InfoSubCommand extends FactionSubCommand
{
    protected bool $requiresPlayer = false;

    public function onBasicRun(CommandSender $sender, array $args): void
    {
        $faction = $sender instanceof Player ? $this->plugin->getPlayerManager()->getPlayerFaction($sender->getUniqueId()) : null;
        if (isset($args["faction"])) {
            $faction = $this->plugin->getFactionsManager()->getFactionByName($args["faction"]);
            if ($faction === null) {
                $this->plugin->getLanguageManager()->sendMessage($sender, "commands.invalid-faction", ["{FACTION}" => $args["faction"]]);
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
            $memberNamesWithRole[] = $this->plugin->getTagManager()->getPlayerRankSymbol($m) . $m->getUsername();
        }

        $this->plugin->getLanguageManager()->sendMessage($sender, "commands.info.message", [
            "{FACTION}" => $faction->getName(),
            "{DESCRIPTION}" => $faction->getDescription() ?? $this->plugin->getLanguageManager()->getMessage($sender instanceof Player ? $this->plugin->getPlayerManager()->getPlayer($sender)->getLanguage() : $this->plugin->getLanguageManager()->getDefaultLanguage(), "commands.info.description-not-set"),
            "{CLAIMS}" => count($this->plugin->getClaimsManager()->getFactionClaims($faction)),
            "{POWER}" => RoundValue::round($faction->getPower()),
            "{TOTALPOWER}" => $faction->getMaxPower(),
            "{CREATIONDATE}" => date("F j, Y @ g:i a T", $faction->getCreationTime()),
            "{AGE}" => FormattedTime::getLong($faction->getCreationTime()),
            "{SIMPLEAGE}" => FormattedTime::getShort($faction->getCreationTime()),
            "{MONEY}" => RoundValue::round($faction->getMoney()),
            "{LEADER}" => ($leader = $faction->getLeader()) === null ? "" : $leader->getUsername(),
            "{ALLIES}" => implode(", ", array_map(function (Faction $f): string {
                return $f->getName();
            }, $faction->getAllies())),
            "{ENEMIES}" => implode(", ", array_map(function (Faction $f): string {
                return $f->getName();
            }, $faction->getEnemies())),
            "{RELATIONS}" => implode(", ", array_map(function (Faction $f) use ($faction): string {
                $color = ["enemy" => $this->plugin->getLanguageManager()->translateColorTags($this->plugin->getConfig()->getNested("symbols.colors.relations.enemy")), "ally" => $this->plugin->getLanguageManager()->translateColorTags($this->plugin->getConfig()->getNested("symbols.colors.relations.ally"))];
                return $color[$faction->getRelation($f)] . $f->getName();
            }, array_merge($faction->getAllies(), $faction->getEnemies()))),
            "{OFFICERS}" => implode(", ", $memberNamesByRole[Roles::OFFICER] ?? []),
            "{MEMBERS}" => implode(", ", $memberNamesByRole[Roles::MEMBER] ?? []),
            "{RECRUITS}" => implode(", ", $memberNamesByRole[Roles::RECRUIT] ?? []),
            "{PLAYERS}" => implode(", ", $memberNamesWithRole),
            "{TOTALPLAYERS}" => count($faction->getMembers()),
            "{ONLINECOUNT}" => count($faction->getOnlineMembers())
        ]);
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("faction", true));
    }
}