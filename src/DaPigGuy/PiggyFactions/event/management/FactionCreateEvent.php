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

    /** @var string */
    private $name;

    public function __construct(Player $player, string $name)
    {
        $this->player = $player;
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}