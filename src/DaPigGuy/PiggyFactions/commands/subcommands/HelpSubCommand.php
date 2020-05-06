<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\commands\FactionCommand;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\PiggyFactions;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class HelpSubCommand extends FactionSubCommand
{
    const COMMANDS_PER_PAGE = 7;

    /** @var FactionCommand */
    private $parentCommand;

    /** @var bool */
    protected $requiresPlayer = false;

    public function __construct(PiggyFactions $plugin, FactionCommand $parentCommand, string $name, string $description = "", array $aliases = [])
    {
        $this->parentCommand = $parentCommand;
        parent::__construct($plugin, $name, $description, $aliases);
    }

    public function onBasicRun(CommandSender $sender, array $args): void
    {
        $subcommands = array_filter($this->parentCommand->getSubCommands(), function (BaseSubCommand $subCommand, string $alias) use ($sender): bool {
            return $subCommand->getName() === $alias && $sender->hasPermission($subCommand->getPermission());
        }, ARRAY_FILTER_USE_BOTH);

        $commandsPerPage = $sender instanceof Player ? self::COMMANDS_PER_PAGE : count($subcommands);
        $maxPages = (int)ceil(count($subcommands) / $commandsPerPage);
        $page = $args["page"] ?? 1;
        $page = $page > $maxPages ? $maxPages : $page;
        $pageCommands = array_slice($subcommands, $commandsPerPage * ($page - 1), $commandsPerPage);

        $language = $sender instanceof Player ? LanguageManager::getInstance()->getPlayerLanguage($sender) : LanguageManager::DEFAULT_LANGUAGE;
        $message = LanguageManager::getInstance()->getMessage($language, "commands.help.header", ["{PAGE}" => $page, "{MAXPAGE}" => $maxPages]);
        foreach ($pageCommands as $pageCommand) {
            $message .= LanguageManager::getInstance()->getMessage($language, "commands.help.command", ["{COMMAND}" => $pageCommand->getName(), "{DESCRIPTION}" => $pageCommand->getDescription()]);
        }
        $sender->sendMessage($message);
    }

    /**
     * @throws ArgumentOrderException
     */
    public function prepare(): void
    {
        $this->registerArgument(0, new IntegerArgument("page", true));
    }
}