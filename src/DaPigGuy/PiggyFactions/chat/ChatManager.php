<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\chat;

use DaPigGuy\PiggyFactions\PiggyFactions;
use pocketmine\Player;

class ChatManager
{
    const ALL_CHAT = "all";
    const FACTION_CHAT = "faction";
    const ALLY_CHAT = "ally"; //TODO: Add allying

    /** @var ChatManager */
    private static $instance;

    /** @var PiggyFactions */
    private $plugin;
    /** @var array */
    private $currentChat;

    public function __construct(PiggyFactions $plugin)
    {
        self::$instance = $this;

        $this->plugin = $plugin;
    }

    public static function getInstance(): ChatManager
    {
        return self::$instance;
    }

    public function getCurrentChat(Player $player): string
    {
        return $this->currentChat[$player->getName()] ?? self::ALL_CHAT;
    }

    public function setCurrentChat(Player $player, string $chat): void
    {
        $this->currentChat[$player->getName()] = $chat;
    }
}