<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\addons\hrkchat;

use CortexPE\HRKChat\event\PlaceholderResolveEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;

class HRKChatTagListener implements Listener
{
    /** @var TagManager */
    private $tagManager;

    public function __construct(TagManager $tagManager)
    {
        $this->tagManager = $tagManager;
    }

    public function onTagResolve(PlaceholderResolveEvent $event): void
    {
        $player = $event->getMember()->getPlayer();
        if (!$player instanceof Player) return;
        $tag = $this->tagManager->getHRKTag($player, $event->getPlaceholderName());
        if ($tag === null) return;
        $event->setValue($tag);
    }
}