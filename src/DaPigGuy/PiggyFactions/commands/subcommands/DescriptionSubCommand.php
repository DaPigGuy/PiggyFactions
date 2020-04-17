<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use pocketmine\Player;

class DescriptionSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, string $aliasUsed, array $args): void
    {
        if (!$faction->hasPermission($faction->getMember($sender->getName()), $this->getName())) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.no-permission");
            return;
        }
        $faction->setDescription($args["description"]);
        LanguageManager::getInstance()->sendMessage($sender, "commands.description.success", ["{DESCRIPTION}" => $args["description"]]);
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("description"));
    }
}