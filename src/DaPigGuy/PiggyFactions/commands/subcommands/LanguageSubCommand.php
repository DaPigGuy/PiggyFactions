<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use DaPigGuy\PiggyFactions\commands\arguments\LanguageEnumArgument;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class LanguageSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        if (!in_array($args["language"], LanguageManager::LANGUAGES)) {
            $member->sendMessage("commands.language.invalid-language");
            return;
        }
        $member->setLanguage($args["language"]);
        $member->sendMessage("commands.language.success");
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new LanguageEnumArgument("language"));
    }
}