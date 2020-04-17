<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
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

        $member = PlayerManager::getInstance()->getPlayer($sender->getUniqueId());
        if ($member === null) return;
        $faction = $member->getFaction();

        if ($this->requiresFaction) {
            if ($faction === null) {
                LanguageManager::getInstance()->sendMessage($sender, "commands.not-in-faction");
                return;
            }
            if (in_array($this->getName(), Faction::PERMISSIONS)) {
                if (!$faction->hasPermission($member, $this->getName())) {
                    LanguageManager::getInstance()->sendMessage($sender, "commands.no-permission");
                    return;
                }
            }
        }

        $this->onNormalRun($sender, $faction, $member, $aliasUsed, $args);
    }

    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {

    }

    public function onFormRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $this->onNormalRun($sender, $faction, $member, $aliasUsed, $args);
    }

    protected function prepare(): void
    {
    }
}