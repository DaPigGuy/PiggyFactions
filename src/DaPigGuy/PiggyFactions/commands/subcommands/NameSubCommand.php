<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class NameSubCommand extends FactionSubCommand
{
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "Please use this command in-game.");
            return;
        }
        $faction = $this->plugin->getPlayerManager()->getPlayerFaction($sender->getUniqueId());
        if ($faction === null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.not-in-faction");
            return;
        }
        if ($this->plugin->getFactionsManager()->getFactionByName($args["name"]) !== null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.create.name-taken", ["{NAME}" => $args["name"]]);
            return;
        }
        if (!$faction->hasPermission($faction->getMember($sender->getName()), $this->getName())) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.no-permission");
            return;
        }
        $faction->setName($args["name"]);
        LanguageManager::getInstance()->sendMessage($sender, "commands.name.success");
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("name"));
    }
}