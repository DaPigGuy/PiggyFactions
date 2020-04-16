<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use DaPigGuy\PiggyFactions\chat\ChatManager;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\PiggyFactions;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ChatSubCommand extends FactionSubCommand
{
    /** @var string */
    private $chat;

    public function __construct(PiggyFactions $plugin, string $chat, string $name, string $description = "", array $aliases = [])
    {
        $this->chat = $chat;
        parent::__construct($plugin, $name, $description, $aliases);
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "Please use this command in-game.");
            return;
        }
        ChatManager::getInstance()->setCurrentChat($sender, ChatManager::getInstance()->getCurrentChat($sender) === $this->chat ? ChatManager::ALL_CHAT : $this->chat);
        LanguageManager::getInstance()->sendMessage($sender, "commands.chat.toggled", ["{CHAT}" => $this->chat, "{TOGGLED}" => ChatManager::getInstance()->getCurrentChat($sender) === $this->chat ? "enabled" : "disabled"]);
    }
}