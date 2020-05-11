<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\member;

use DaPigGuy\PiggyFactions\event\FactionMemberEvent;
use pocketmine\event\Cancellable;

class FactionLeaveEvent extends FactionMemberEvent implements Cancellable
{
}