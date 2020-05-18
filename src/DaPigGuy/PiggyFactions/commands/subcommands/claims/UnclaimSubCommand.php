<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\claims;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\claims\ClaimsManager;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\claims\UnclaimAllChunksEvent;
use DaPigGuy\PiggyFactions\event\claims\UnclaimChunkEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class UnclaimSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        if (($args["type"] ?? null) === "all") {
            $ev = new UnclaimAllChunksEvent($faction, $member);
            $ev->call();
            if ($ev->isCancelled()) return;

            foreach (ClaimsManager::getInstance()->getFactionClaims($faction) as $claim) {
                ClaimsManager::getInstance()->deleteClaim($claim);
            }
            LanguageManager::getInstance()->sendMessage($sender, "commands.unclaim.all.success");
            return;
        }

        $claim = ClaimsManager::getInstance()->getClaim($sender->getLevel(), $sender->getLevel()->getChunkAtPosition($sender));
        if ($claim === null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.unclaim.not-claimed");
            return;
        }
        if ($claim->getFaction() !== $faction && !$member->isInAdminMode()) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.unclaim.other-faction");
            return;
        }

        $ev = new UnclaimChunkEvent($faction, $member, $claim);
        $ev->call();
        if ($ev->isCancelled()) return;

        ClaimsManager::getInstance()->deleteClaim($claim);
        LanguageManager::getInstance()->sendMessage($sender, "commands.unclaim.success");
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("type", true));
    }
}