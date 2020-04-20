<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\claims;

use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\players\PlayerManager;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\tile\Container;

class ClaimsListener implements Listener
{
    /** @var PiggyFactions */
    private $plugin;
    /** @var ClaimsManager */
    private $manager;

    public function __construct(PiggyFactions $plugin, ClaimsManager $claimsManager)
    {
        $this->plugin = $plugin;
        $this->manager = $claimsManager;
    }

    public function onBreak(BlockBreakEvent $event): void
    {
        if (!$this->canAffectArea($event->getPlayer(), $event->getBlock())) $event->setCancelled();
    }

    public function onPlace(BlockPlaceEvent $event): void
    {
        if (!$this->canAffectArea($event->getPlayer(), $event->getBlock())) $event->setCancelled();
    }

    public function onInteract(PlayerInteractEvent $event): void
    {
        $tile = $event->getBlock()->getLevel()->getTile($event->getBlock());
        if (!$this->canAffectArea($event->getPlayer(), $event->getBlock(), $tile instanceof Container ? "container" : "interact")) $event->setCancelled();
    }

    public function canAffectArea(Player $player, Position $position, string $type = "build"): bool
    {
        $member = PlayerManager::getInstance()->getPlayer($player->getUniqueId());
        $claim = $this->manager->getClaim($position->getLevel(), $position->getLevel()->getChunkAtPosition($position));
        if ($claim !== null) {
            return $claim->getFaction()->hasPermission($member, $type);
        }
        return true;
    }
}