<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\member;

use DaPigGuy\PiggyFactions\event\FactionMemberEvent;
use DaPigGuy\PiggyFactions\logs\FactionLog;
use DaPigGuy\PiggyFactions\logs\LogsManager;
use pocketmine\event\Cancellable;

class FactionJoinEvent extends FactionMemberEvent implements Cancellable
{
    public function call(): void
    {
        $factionLog = new FactionLog(FactionLog::JOIN, ["joined" => $this->getMember()->getUsername()]);
        LogsManager::getInstance()->addFactionLog($this->getFaction(), $factionLog);
        parent::call();
    }
}