<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\member;

use DaPigGuy\PiggyFactions\event\FactionMemberEvent;
use DaPigGuy\PiggyFactions\logs\FactionLog;
use DaPigGuy\PiggyFactions\logs\LogsManager;
use pocketmine\event\Cancellable;

class FactionLeaveEvent extends FactionMemberEvent implements Cancellable
{

}