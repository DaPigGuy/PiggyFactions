<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\management;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\logs\LogsManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class LogsSubCommand extends FactionSubCommand
{
    const ENTRIES_PER_PAGE = 10;

    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $currentPage = (int)(($args["page"] ?? 1) - 1);
        if (isset($args["action"]) && is_numeric($args["action"])) $currentPage = (int)$args["action"] - 1;
        if ($currentPage < 0) {
            $member->sendMessage("logs.page-too-low");
            return;
        }

        $offset = $currentPage * LogsSubCommand::ENTRIES_PER_PAGE;

        if (!isset($args["action"]) || is_numeric($args["action"])) {
            if ($this->plugin->areFormsEnabled()) {
                $this->sendLogsForm($sender, $faction, $currentPage);
                return;
            }
            LogsManager::getInstance()->sendAllLogsTitle($faction, $sender, $currentPage);
            LogsManager::getInstance()->sendAllLogs($faction, $sender, $offset);
            return;
        }

        $action = $args["action"];
        if ($this->plugin->areFormsEnabled()) {
            $this->sendLogsForm($sender, $faction, $currentPage, ($action));
            return;
        }
        LogsManager::getInstance()->sendLogsTitle($faction, $sender, $action, $currentPage);
        LogsManager::getInstance()->sendLogsByAction($faction, $sender, $action, $offset);
    }

    public function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("action", true));
        $this->registerArgument(1, new IntegerArgument("page", true));
    }

    private function sendLogsForm(Player $player, Faction $faction, int $currentPage, ?string $action = null): void
    {
        $form = new SimpleForm(function (Player $player, ?int $data) use ($faction, $currentPage, $action) {
            if ($data !== null) {
                switch ($data) {
                    case 0:
                        $this->sendLogsForm($player, $faction, $currentPage + ($currentPage === 0 ? 1 : -1), $action);
                        return;
                    case 1:
                        if ($currentPage !== 0) $this->sendLogsForm($player, $faction, $currentPage + 1, $action);
                        return;
                }
            }
        });

        if ($currentPage !== 0) $form->addButton(TextFormat::BOLD . TextFormat::BLUE . "Previous");
        $form->addButton(TextFormat::BOLD . TextFormat::BLUE . "Next");
        $form->addButton(TextFormat::BOLD . TextFormat::RED . "Close");

        $offset = $currentPage * LogsSubCommand::ENTRIES_PER_PAGE;
        LogsManager::getInstance()->sendLogsForm($faction, $form, $player, $offset, $action);
    }
}