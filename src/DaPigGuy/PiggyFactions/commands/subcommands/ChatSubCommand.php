<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use DaPigGuy\PiggyFactions\chat\ChatManager;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class ChatSubCommand extends FactionSubCommand
{
    /** @var string */
    private $chat;

    public function __construct(PiggyFactions $plugin, string $chat, string $name, string $description = "", array $aliases = [])
    {
        $this->chat = $chat;
        parent::__construct($plugin, $name, $description, $aliases);
    }

    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        ChatManager::getInstance()->setCurrentChat($sender, ChatManager::getInstance()->getCurrentChat($sender) === $this->chat ? ChatManager::ALL_CHAT : $this->chat);
        LanguageManager::getInstance()->sendMessage($sender, "commands.chat.toggled", ["{CHAT}" => $this->chat, "{TOGGLED}" => ChatManager::getInstance()->getCurrentChat($sender) === $this->chat ? "enabled" : "disabled"]);
    }
}