<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\flags\Flag;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class TopSubCommand extends FactionSubCommand
{
    const PAGE_LENGTH = 10;

    /** @var bool */
    protected $requiresPlayer = false;

    public function onBasicRun(CommandSender $sender, array $args): void
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
        if ($this->plugin->isFactionBankEnabled()) $types["money"] = function (Faction $a, Faction $b): int {
            return (int)($b->getMoney() - $a->getMoney());
        };
        $type = $args["type"] ?? "power";
        $page = ($args["page"] ?? 1) - 1;
        if (!isset($types[$type])) {
            return;
        }

        $factions = array_filter($this->plugin->getFactionsManager()->getFactions(), function (Faction $faction): bool {
            return !$faction->getFlag(Flag::SAFEZONE) && !$faction->getFlag(Flag::WARZONE);
        });
        usort($factions, $types[$type]);

        $language = $sender instanceof Player ? $this->plugin->getLanguageManager()->getPlayerLanguage($sender) : $this->plugin->getLanguageManager()->getDefaultLanguage();
        $message = $this->plugin->getLanguageManager()->getMessage($language, "commands.top.header", ["{PAGE}" => $page + 1, "{TOTALPAGES}" => ceil(count($factions) / self::PAGE_LENGTH), "{CATEGORY}" => ucfirst($type)]);
        foreach (array_slice($factions, $page * self::PAGE_LENGTH, self::PAGE_LENGTH) as $rank => $faction) {
            $message .= TextFormat::EOL . $this->plugin->getLanguageManager()->getMessage($language, "commands.top.line." . $type, [
                    "{RELATIONCOLOR}" => $sender instanceof Player ? $this->plugin->getLanguageManager()->getColorFor($sender, $faction) : $this->plugin->getConfig()->getNested("symbols.colors.neutral", TextFormat::WHITE),
                    "{RANK}" => $rank + 1 + $page * self::PAGE_LENGTH,
                    "{FACTION}" => $faction->getName(),
                    "{ONLINE}" => count($faction->getOnlineMembers()),
                    "{MEMBERS}" => count($faction->getMembers()),
                    "{POWER}" => round($faction->getPower(), 2, PHP_ROUND_HALF_DOWN),
                    "{TOTALPOWER}" => count($faction->getMembers()) * $this->plugin->getConfig()->getNested("factions.power.max"),
                    "{MONEY}" => round($faction->getMoney(), 2, PHP_ROUND_HALF_DOWN)
                ]);
        }
        $sender->sendMessage($message);
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("type", true));
        $this->registerArgument(1, new IntegerArgument("page", true));
    }
}