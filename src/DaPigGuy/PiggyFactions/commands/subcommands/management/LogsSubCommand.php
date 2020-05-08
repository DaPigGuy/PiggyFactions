<?php

namespace DaPigGuy\PiggyFactions\commands\subcommands\management;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\logs\LogsManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class LogsSubCommand extends FactionSubCommand
{
    const ENTRIES_PER_PAGE = 10;

    //TODO: forms
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $page = 0;
        if(isset($args["page"])) {
            $page = (int)$args["page"];
        }
        $offset = $page * LogsSubCommand::ENTRIES_PER_PAGE;
        //TODO: Count total entries and get total number of pages to look cool. f.e: Page 1/69
        LanguageManager::getInstance()->sendMessage($sender, "logs.title");
        if(!isset($args["action"])) {
            LogsManager::getInstance()->sendAllLogs($faction, $sender, $offset);
            return;
        }
        LogsManager::getInstance()->sendLogsByAction($faction, $sender, $args["action"], $offset);
    }

    /**
     * @throws ArgumentOrderException
     */
    public function prepare(): void
    {
        //TODO: Someone pls help how do I allow /f logs <page> and also /f logs <action> <page>
        $this->registerArgument(0, new RawStringArgument("action", true));
        $this->registerArgument(1, new IntegerArgument("page", true));
    }
}