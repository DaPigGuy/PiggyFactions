<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\claims\unclaim;

use DaPigGuy\PiggyFactions\claims\ClaimsManager;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\claims\UnclaimChunkEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class UnclaimSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $claim = ClaimsManager::getInstance()->getClaim($sender->getLevel(), $sender->getLevel()->getChunkAtPosition($sender));
        if ($claim === null) {
            $member->sendMessage("commands.unclaim.not-claimed");
            return;
        }
        if ($claim->getFaction() !== $faction && !$member->isInAdminMode()) {
            $member->sendMessage("commands.unclaim.other-faction");
            return;
        }

        $ev = new UnclaimChunkEvent($faction, $member, $claim);
        $ev->call();
        if ($ev->isCancelled()) return;

        ClaimsManager::getInstance()->deleteClaim($claim);
        $member->sendMessage("commands.unclaim.success");
    }

    protected function prepare(): void
    {
        $this->registerSubCommand(new UnclaimAllSubCommand($this->plugin, "all"));
        $this->registerSubCommand(new UnclaimAutoSubCommand($this->plugin, "auto"));
        $this->registerSubCommand(new UnclaimCircleSubCommand($this->plugin, "circle"));
        $this->registerSubCommand(new UnclaimSquareSubCommand($this->plugin, "square"));
    }
}