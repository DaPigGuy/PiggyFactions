<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\claims;

use DaPigGuy\PiggyFactions\event\FactionMemberEvent;
use pocketmine\event\Cancellable;

class UnclaimAllChunksEvent extends FactionMemberEvent implements Cancellable
{
}