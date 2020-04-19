<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\management;

use DaPigGuy\PiggyFactions\event\FactionEvent;
use pocketmine\event\Cancellable;

class FactionDisbandEvent extends FactionEvent implements Cancellable
{
}