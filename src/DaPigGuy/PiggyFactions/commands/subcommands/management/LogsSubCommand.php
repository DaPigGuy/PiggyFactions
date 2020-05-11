<?php

namespace DaPigGuy\PiggyFactions\commands\subcommands\management;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\logs\LogsManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class LogsSubCommand extends FactionSubCommand
{
    const ENTRIES_PER_PAGE = 10;

    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $currentPage = 0;
        if(isset($args["page"])) {
            $currentPage = (int)$args["page"];
        }
        if(isset($args["action"]) && is_numeric($args["action"])) {
            $currentPage = (int) $args["action"]; //very epic :| this is to allow both /f logs <page> and /f logs <action> <page>
        }
        $offset = $currentPage * LogsSubCommand::ENTRIES_PER_PAGE;

        if(!isset($args["action"]) || is_numeric($args["action"])) {
            LogsManager::getInstance()->sendAllLogsTitle($faction, $sender, $currentPage);
            LogsManager::getInstance()->sendAllLogs($faction, $sender, $offset);
            return;
        }
        LogsManager::getInstance()->sendLogsTitle($faction, $sender, $args["action"], $currentPage);
        LogsManager::getInstance()->sendLogsByAction($faction, $sender, $args["action"], $offset);
    }

    /**
     * @throws ArgumentOrderException
     */
    public function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("action", true));
        $this->registerArgument(1, new IntegerArgument("page", true));
    }
}