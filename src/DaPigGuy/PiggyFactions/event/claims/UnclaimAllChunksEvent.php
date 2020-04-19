<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\claims;

use DaPigGuy\PiggyFactions\event\FactionEvent;
use pocketmine\event\Cancellable;

class UnclaimAllChunksEvent extends FactionEvent implements Cancellable
{
}