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
        if (!$this->canAffectArea($event->getPlayer(), $event->getBlock())) $event->setCancelled();
    }

    public function canAffectArea(Player $player, Position $position): bool
    {
        $faction = PlayerManager::getInstance()->getPlayerFaction($player->getUniqueId());
        $claim = $this->manager->getClaim($position->getLevel(), $position->getLevel()->getChunkAtPosition($position));
        if ($claim !== null && $faction !== null) {
            if ($faction->getId() !== $claim->getFaction()->getId()) {
                return false;
            }
        }
        return true;
    }
}