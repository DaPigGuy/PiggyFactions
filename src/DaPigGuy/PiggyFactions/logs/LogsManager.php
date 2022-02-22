<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\logs;

use DaPigGuy\PiggyFactions\commands\subcommands\management\LogsSubCommand;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\PiggyFactions;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

class LogsManager
{
    private static LogsManager $instance;

    public function __construct(private PiggyFactions $plugin)
    {
        self::$instance = $this;
    }

    public static function getInstance(): LogsManager
    {
        return self::$instance;
    }

    public function sendLogsForm(Faction $faction, SimpleForm $form, Player $player, int $offset = 0, ?string $action = null, int $count = 10, callable $onSelect = null): void
    {
        $playerLang = LanguageManager::getInstance()->getPlayerLanguage($player);
        $form->setTitle(LanguageManager::getInstance()->getMessage($playerLang, "logs.title", ["{CURRENTPAGE}" => $offset / LogsSubCommand::ENTRIES_PER_PAGE + 1, "/{TOTALPAGES}" => ""]));

        if ($onSelect === null) {
            $onSelect = function (array $rows) use ($form, $player, $playerLang): void {
                $message = $this->parseDataToMessage($rows, $playerLang);
                $message = str_replace("=>", ":\n", $message);
                if ($message === "") $message = LanguageManager::getInstance()->getMessage($playerLang, "logs.invalid-log");
                $form->setContent($message);
                $player->sendForm($form);
            };
        }
        if ($action === null) {
            $psVars = ["faction" => $faction->getId(), "count" => $count, "startpoint" => $offset];
            $this->plugin->getDatabase()->executeSelect("piggyfactions.logs.loadall", $psVars, $onSelect);
        } else {
            $psVars = ["action" => $action, "faction" => $faction->getId(), "count" => $count, "startpoint" => $offset];
            $this->plugin->getDatabase()->executeSelect("piggyfactions.logs.load", $psVars, $onSelect);
        }
    }

    public function sendAllLogs(Faction $faction, Player $player, int $offset = 0, int $count = 10, callable $onSelect = null): void
    {
        if ($onSelect === null) {
            $onSelect = function (array $rows) use ($player): void {
                $message = $this->parseDataToMessage($rows, LanguageManager::getInstance()->getPlayerLanguage($player));
                $player->sendMessage($message);
            };
        }
        $psVars = ["faction" => $faction->getId(), "count" => $count, "startpoint" => $offset];
        $this->plugin->getDatabase()->executeSelect("piggyfactions.logs.loadall", $psVars, $onSelect);
    }

    public function sendAllLogsTitle(Faction $faction, Player $player, int $currentPage): void
    {
        $onSelect = function (array $rows) use ($player, $currentPage): void {
            $totalCount = count($rows);
            $totalPages = ceil($totalCount / LogsSubCommand::ENTRIES_PER_PAGE) - 1;
            LanguageManager::getInstance()->sendMessage($player, "logs.title", ["{CURRENTPAGE}" => $currentPage + 1, "{TOTALPAGES}" => $totalPages]);
        };
        $psVars = ["faction" => $faction->getId()];
        $this->plugin->getDatabase()->executeSelect("piggyfactions.logs.countall", $psVars, $onSelect);
    }

    public function sendLogsByAction(Faction $faction, Player $player, string $action, int $offset = 0, int $count = 10, callable $onSelect = null): void
    {
        if ($onSelect === null) {
            $onSelect = function (array $rows) use ($player): void {
                if (count($rows) === 0) {
                    LanguageManager::getInstance()->sendMessage($player, "logs.invalid-log");
                    return;
                }
                $message = $this->parseDataToMessage($rows, LanguageManager::getInstance()->getPlayerLanguage($player));
                $player->sendMessage($message);
            };
        }
        $psVars = ["action" => $action, "faction" => $faction->getId(), "count" => $count, "startpoint" => $offset];
        $this->plugin->getDatabase()->executeSelect("piggyfactions.logs.load", $psVars, $onSelect);
    }

    public function sendLogsTitle(Faction $faction, Player $player, string $action, int $currentPage): void
    {
        $onSelect = function (array $rows) use ($player, $currentPage): void {
            $totalCount = count($rows);
            $totalPages = ceil($totalCount / LogsSubCommand::ENTRIES_PER_PAGE) - 1;
            LanguageManager::getInstance()->sendMessage($player, "logs.title", ["{CURRENTPAGE}" => $currentPage + 1, "{TOTALPAGES}" => $totalPages]);
        };
        $psVars = ["faction" => $faction->getId(), "action" => $action];
        $this->plugin->getDatabase()->executeSelect("piggyfactions.logs.count", $psVars, $onSelect);
    }

    public function addFactionLog(Faction $faction, FactionLog $factionLog): void
    {
        $action = $factionLog->getName();
        $psVars = ["faction" => $faction->getId(), "action" => $action, "timestamp" => time(), "data" => json_encode($factionLog)];
        $this->plugin->getDatabase()->executeInsert("piggyfactions.logs.create", $psVars);
    }

    private function parseDataToMessage(array $rows, string $language): string
    {
        $message = "";
        foreach ($rows as $row) {
            $data = json_decode($row["data"], true);
            $message .= $this->parseMessageFromAction($row["action"], $data, $language) . "\n";
            $message = str_replace("{TIME}", date("Y-m-d H:i:s", $row["timestamp"]), $message);
        }
        return $message;
    }

    private function parseMessageFromAction(string $action, array $data, string $language): string
    {
        return match ($action) {
            FactionLog::KICK => LanguageManager::getInstance()->getMessage($language, "logs.actions.kick", ["{KICKED}" => $data["kicked"], "{KICKER}" => $data["kicker"]]),
            FactionLog::BAN => LanguageManager::getInstance()->getMessage($language, "logs.actions.ban", ["{BANNED}" => $data["banned"], "{BANNEDBY}" => $data["bannedBy"]]),
            FactionLog::UNBAN => LanguageManager::getInstance()->getMessage($language, "logs.actions.unban", ["{UNBANNED}" => $data["unbanned"], "{UNBANNEDBY}" => $data["unbannedBy"]]),
            FactionLog::INVITE => LanguageManager::getInstance()->getMessage($language, "logs.actions.invite", ["{INVITED}" => $data["invited"], "{INVITEDBY}" => $data["invitedBy"]]),
            FactionLog::LEADER_CHANGE => LanguageManager::getInstance()->getMessage($language, "logs.actions.leader_change", ["{NEW}" => $data["new"], "{OLD}" => $data["old"]]),
            FactionLog::JOIN => LanguageManager::getInstance()->getMessage($language, "logs.actions.join", ["{JOINED}" => $data["joined"]]),
            FactionLog::LEAVE => LanguageManager::getInstance()->getMessage($language, "logs.actions.leave", ["{LEFT}" => $data["left"]]),
            default => "",
        };
    }
}