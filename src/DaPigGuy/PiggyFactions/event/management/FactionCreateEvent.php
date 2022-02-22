<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\management;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

class FactionCreateEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(Player $player, private string $name)
    {
        $this->player = $player;
    }

    public function getName(): string
    {
        return $this->name;
    }
}