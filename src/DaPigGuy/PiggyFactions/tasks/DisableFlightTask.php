<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\tasks;

use DaPigGuy\PiggyFactions\claims\ClaimsManager;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\permissions\FactionPermission;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;
use pocketmine\scheduler\Task;

class DisableFlightTask extends Task
{
    /** @var Player */
    private $player;
    /** @var FactionsPlayer */
    private $member;
    /** @var int */
    private $duration = 5;

    public function __construct(Player $player, FactionsPlayer $member)
    {
        $this->player = $player;
        $this->member = $member;
    }

    public function onRun(int $currentTick): void
    {
        $claim = ClaimsManager::getInstance()->getClaim($this->player->getLevel(), $this->player->getLevel()->getChunkAtPosition($this->player));
        if (($claim !== null && $claim->getFaction()->hasPermission($this->member, FactionPermission::FLY)) || !$this->player->isOnline()) {
            $this->getHandler()->cancel();
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