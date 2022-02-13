<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\utils\ChatTypes;
use pocketmine\player\Player;

class ChatSubCommand extends FactionSubCommand
{
    private string $chat;

    public function __construct(PiggyFactions $plugin, string $chat, string $name, string $description = "", array $aliases = [])
    {
        $this->chat = $chat;
        parent::__construct($plugin, $name, $description, $aliases);
    }

    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $member->setCurrentChat($member->getCurrentChat() === $this->chat ? ChatTypes::ALL : $this->chat);
        $member->sendMessage("commands.chat.toggled" . ($member->getCurrentChat() === $this->chat ? "" : "-off"), ["{CHAT}" => $this->chat]);
    }
}