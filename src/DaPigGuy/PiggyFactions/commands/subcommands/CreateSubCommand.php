<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class CreateSubCommand extends FactionSubCommand
{
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "Please use this command in-game.");
            return;
        }
        if ($this->plugin->getPlayerManager()->getPlayerFaction($sender->getUniqueId()) !== null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.already-in-faction");
            return;
        }
        if ($this->plugin->getFactionsManager()->getFactionByName($args["name"]) !== null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.create.name-taken", ["{NAME}" => $args["name"]]);
            return;
        }
        $this->plugin->getFactionsManager()->createFaction($args["name"], $sender);
        LanguageManager::getInstance()->sendMessage($sender, "commands.create.success", ["{NAME}" => $args["name"]]);
    }

    /**
     * @throws ArgumentOrderException
     */
    public function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("name"));
    }
}