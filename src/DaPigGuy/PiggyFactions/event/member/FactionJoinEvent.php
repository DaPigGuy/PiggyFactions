<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\member;

use DaPigGuy\PiggyFactions\event\FactionMemberEvent;
use pocketmine\event\Cancellable;

class FactionJoinEvent extends FactionMemberEvent implements Cancellable
{
}