<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\factions\FactionsManager;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class TopSubCommand extends FactionSubCommand
{
    const PAGE_LENGTH = 10;

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $types = [
            "online" => function (Faction $a, Faction $b): int {
                return count($b->getOnlineMembers()) - count($a->getOnlineMembers());
            },
            "members" => function (Faction $a, Faction $b): int {
                return count($b->getMembers()) - count($a->getMembers());
            },
            "power" => function (Faction $a, Faction $b): int {
                return (int)($b->getPower() - $a->getPower());
            }
        ];
        $type = $args["type"] ?? "power";
        $page = ($args["page"] ?? 1) - 1;
        if (!isset($types[$type])) {
            return;
        }

        $factions = FactionsManager::getInstance()->getFactions();
        usort($factions, $types[$type]);

        $language = $sender instanceof Player ? LanguageManager::getInstance()->getPlayerLanguage($sender) : LanguageManager::DEFAULT_LANGUAGE;
        $message = LanguageManager::getInstance()->getMessage($language, "commands.top.header", ["{PAGE}" => $page + 1, "{TOTALPAGES}" => ceil(count($factions) / self::PAGE_LENGTH), "{CATEGORY}" => ucfirst($type)]);
        foreach (array_slice($factions, $page * self::PAGE_LENGTH, self::PAGE_LENGTH) as $rank => $faction) {
            $message .= TextFormat::EOL . LanguageManager::getInstance()->getMessage($language, "commands.top.line." . $type, [
                    "{RELATIONCOLOR}" => LanguageManager::getInstance()->getColorFor($sender, $faction),
                    "{RANK}" => $rank + 1 + $page * self::PAGE_LENGTH,
                    "{FACTION}" => $faction->getName(),
                    "{ONLINE}" => count($faction->getOnlineMembers()),
                    "{MEMBERS}" => count($faction->getMembers()),
                    "{POWER}" => $faction->getPower(),
                    "{TOTALPOWER}" => count($faction->getMembers()) * $this->plugin->getConfig()->getNested("factions.power.max")
                ]);
        }
        $sender->sendMessage($message);
    }

    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        //NOOP
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("type", true));
        $this->registerArgument(1, new IntegerArgument("page", true));
    }
}