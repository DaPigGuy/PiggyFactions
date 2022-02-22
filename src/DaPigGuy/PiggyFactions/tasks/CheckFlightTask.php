<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\tasks;

use DaPigGuy\PiggyFactions\claims\ClaimsManager;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\permissions\FactionPermission;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;

class CheckFlightTask extends Task
{
    private int $duration = 5;

    public function __construct(private Player $player, private FactionsPlayer $member)
    {
    }

    public function onRun(): void
    {
        if (!$this->player->isOnline() || !$this->member->isFlying()) {
            $this->member->setFlying(false);
            $this->getHandler()->cancel();
            return;
        }
        $claim = ClaimsManager::getInstance()->getClaimByPosition($this->player->getPosition());
        if ($claim !== null && $claim->getFaction()->hasPermission($this->member, FactionPermission::FLY)) {
            $this->duration = 5;
            return;
        }
        $this->player->sendTip(LanguageManager::getInstance()->getMessage($this->member->getLanguage(), "claims.flight-disable-warning", ["{AMOUNT}" => $this->duration]));
        $this->duration--;
        if ($this->duration === 0) {
            $this->getHandler()->cancel();
            $this->member->setFlying(false);
            $this->player->setFlying(false);
            $this->player->setAllowFlight(false);
        }
    }
}