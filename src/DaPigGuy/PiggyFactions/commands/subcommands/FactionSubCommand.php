<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\players\PlayerManager;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

abstract class FactionSubCommand extends BaseSubCommand
{
    /** @var PiggyFactions */
    protected $plugin;
    /** @var bool */
    protected $requiresFaction = true;

    public function __construct(PiggyFactions $plugin, string $name, string $description = "", array $aliases = [])
    {
        $this->plugin = $plugin;
        $this->setPermission("piggyfactions.command.faction." . $name);
        parent::__construct($name, $description, $aliases);
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "Please use this command in-game.");
            return;
        }
        $faction = PlayerManager::getInstance()->getPlayerFaction($sender->getUniqueId());
        if ($faction === null && $this->requiresFaction) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.not-in-faction");
            return;
        }
        $this->onNormalRun($sender, $faction, $aliasUsed, $args);
    }

    public function onNormalRun(Player $sender, ?Faction $faction, string $aliasUsed, array $args): void
    {

    }

    public function onFormRun(Player $sender, ?Faction $faction, string $aliasUsed, array $args): void
    {
        $this->onNormalRun($sender, $faction, $aliasUsed, $args);
    }

    protected function prepare(): void
    {
    }
}