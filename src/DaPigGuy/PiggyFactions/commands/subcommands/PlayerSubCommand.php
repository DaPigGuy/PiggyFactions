<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use CortexPE\Commando\args\TextArgument;
use DaPigGuy\PiggyFactions\utils\FormattedTime;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class PlayerSubCommand extends FactionSubCommand
{
    /** @var bool */
    protected $requiresPlayer = false;

    public function onBasicRun(CommandSender $sender, array $args): void
    {
        $player = $sender instanceof Player ? $this->plugin->getPlayerManager()->getPlayer($sender) : null;
        if (isset($args["player"])) {
            $player = $this->plugin->getPlayerManager()->getPlayerByName($args["player"]);
            if ($player === null) {
                $this->plugin->getLanguageManager()->sendMessage($sender, "commands.invalid-player", ["{PLAYER}" => $args["player"]]);
                return;
            }
        }
        if ($player === null) {
            $this->sendUsage();
            return;
        }

        $config = $this->plugin->getConfig();
        $time = round(($player->getMaxPower() - $player->getPower()) / $config->getNested("factions.power.per.hour"), 2, PHP_ROUND_HALF_DOWN);
        $firstPlayed = (int)($this->plugin->getServer()->getOfflinePlayer($player->getUsername())->getFirstPlayed() / 1000);
        $faction = $player->getFaction();
        $this->plugin->getLanguageManager()->sendMessage($sender, "commands.player.message", [
            "{PLAYER}" => $player->getUsername(),
            "{FACTION}" => $faction === null ? "None" : $faction->getName(),
            "{RANKSYMBOL}" => $this->plugin->getTagManager()->getPlayerRankSymbol($player),
            "{POWER}" => round($player->getPower(), 2, PHP_ROUND_HALF_DOWN),
            "{TOTALPOWER}" => $player->getMaxPower(),
            "{TIMETOMAXPOWER}" => $time <= 0 ? "" : $this->plugin->getLanguageManager()->getMessage($sender instanceof Player ? $this->plugin->getPlayerManager()->getPlayer($sender)->getLanguage() : $this->plugin->getLanguageManager()->getDefaultLanguage(), "commands.player.time-to-max-power", ["{TIME}" => $time]),
            "{POWERPERHOUR}" => round($config->getNested("factions.power.per.hour"), 2, PHP_ROUND_HALF_DOWN),
            "{POWERPERDEATH}" => round($config->getNested("factions.power.per.death"), 2, PHP_ROUND_HALF_DOWN),
            "{CREATIONDATE}" => date("F j, Y @ g:i a T", $firstPlayed),
            "{AGE}" => FormattedTime::getLong($firstPlayed),
            "{SIMPLEAGE}" => FormattedTime::getShort($firstPlayed)
        ]);
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("player", true));
    }
}