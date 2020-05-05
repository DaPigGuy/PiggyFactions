<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\BaseSubCommand;
use DaPigGuy\PiggyFactions\commands\FactionCommand;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class HelpSubCommand extends FactionSubCommand
{
    const COMMANDS_PER_PAGE = 5;

    /** @var FactionCommand */
    private $parentCommand;

    public function __construct(PiggyFactions $plugin, FactionCommand $parentCommand, string $name, string $description = "", array $aliases = [])
    {
        $this->parentCommand = $parentCommand;
        parent::__construct($plugin, $name, $description, $aliases);
    }

    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $subcommands = array_filter($this->parentCommand->getSubCommands(), function (BaseSubCommand $subCommand, string $alias) use ($sender): bool {
            return $subCommand->getName() === $alias && $sender->hasPermission($subCommand->getPermission());
        }, ARRAY_FILTER_USE_BOTH);

        $maxPages = (int)ceil(count($subcommands) / self::COMMANDS_PER_PAGE);
        $page = $args["page"] ?? 1;
        $page = $page > $maxPages ? $maxPages : $page;
        $pageCommands = array_slice($subcommands, self::COMMANDS_PER_PAGE * ($page - 1), self::COMMANDS_PER_PAGE);

        $message = LanguageManager::getInstance()->getMessage(LanguageManager::getInstance()->getPlayerLanguage($sender), "commands.help.header", ["{PAGE}" => $page, "{MAXPAGE}" => $maxPages]);
        foreach ($pageCommands as $pageCommand) {
            $message .= LanguageManager::getInstance()->getMessage(LanguageManager::getInstance()->getPlayerLanguage($sender), "commands.help.command", ["{COMMAND}" => $pageCommand->getName(), "{DESCRIPTION}" => $pageCommand->getDescription()]);
        }
        $sender->sendMessage($message);
    }

    public function prepare(): void
    {
        $this->registerArgument(0, new IntegerArgument("page", true));
    }
}