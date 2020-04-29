<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\flags;

use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\flags\FlagFactory;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class FlagSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        if (($flag = FlagFactory::getFlag($args["flag"])) === null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.flag.invalid-flag", ["{FLAG}" => $args["flag"]]);
            return;
        }
        if (!$flag->isEditable() && !$member->isInAdminMode()) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.flag.not-editable", ["{FLAG}" => $args["flag"]]);
            return;
        }
        $faction->setFlag($args["flag"], !$faction->getFlag($args["flag"]));
        LanguageManager::getInstance()->sendMessage($sender, "commands.flag.toggled" . ($faction->getFlag($args["flag"]) ? "" : "-off"), ["{FLAG}" => $args["flag"]]);
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("flag"));
    }
}