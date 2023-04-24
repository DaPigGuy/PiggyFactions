<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use DaPigGuy\PiggyFactions\libs\CortexPE\Commando\args\IntegerArgument;
use DaPigGuy\PiggyFactions\libs\CortexPE\Commando\BaseSubCommand;
use DaPigGuy\PiggyFactions\commands\FactionCommand;
use DaPigGuy\PiggyFactions\PiggyFactions;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class HelpSubCommand extends FactionSubCommand
{
    const COMMANDS_PER_PAGE = 7;

    private FactionCommand $parentCommand;

    protected bool $requiresPlayer = false;

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
        $page = min($page, $maxPages);
        $pageCommands = array_slice($subcommands, $commandsPerPage * ($page - 1), $commandsPerPage);

        $language = $sender instanceof Player ? $this->plugin->getLanguageManager()->getPlayerLanguage($sender) : $this->plugin->getLanguageManager()->getDefaultLanguage();
        $message = $this->plugin->getLanguageManager()->getMessage($language, "commands.help.header", ["{PAGE}" => $page, "{MAXPAGE}" => $maxPages]);
        foreach ($pageCommands as $pageCommand) {
            $message .= $this->plugin->getLanguageManager()->getMessage($language, "commands.help.command", ["{COMMAND}" => $pageCommand->getName(), "{DESCRIPTION}" => $pageCommand->getDescription()]);
        }
        $sender->sendMessage($message);
    }

    public function prepare(): void
    {
        $this->registerArgument(0, new IntegerArgument("page", true));
    }
}