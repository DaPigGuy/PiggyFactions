<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\claims;

use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\permissions\FactionPermission;
use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\players\PlayerManager;
use DaPigGuy\PiggyFactions\tasks\DisableFlightTask;
use DaPigGuy\PiggyFactions\utils\Relations;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
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

    public function onCommandPreprocess(PlayerCommandPreprocessEvent $event): void
    {
        $player = $event->getPlayer();
        $message = $event->getMessage();
        $member = PlayerManager::getInstance()->getPlayer($player->getUniqueId());
        $faction = $member->getFaction();
        $claim = ClaimsManager::getInstance()->getClaim($player->getLevel(), $player->getLevel()->getChunkAtPosition($player));
        if (!$member->isInAdminMode() && $claim !== null && $claim->getFaction() !== $faction) {
            $relation = $faction === null ? Relations::NONE : $faction->getRelation($claim->getFaction());
            if (substr($message, 0, 1) === "/") {
                $command = substr(explode(" ", $message)[0], 1);
                if (in_array($command, $this->plugin->getConfig()->getNested("factions.claims.denied-commands." . $relation, []))) {
                    LanguageManager::getInstance()->sendMessage($player, "claims.command-denied", ["{COMMAND}" => $command, "{RELATION}" => $relation === "none" ? "neutral" : $relation]);
                    $event->setCancelled();
                }
            }
        }
    }

    public function onInteract(PlayerInteractEvent $event): void
    {
        $tile = $event->getBlock()->getLevel()->getTile($event->getBlock());
        if (!$this->canAffectArea($event->getPlayer(), $event->getBlock(), $tile instanceof Container ? FactionPermission::CONTAINERS : FactionPermission::INTERACT)) $event->setCancelled();
    }

    public function onMove(PlayerMoveEvent $event): void
    {
        $player = $event->getPlayer();
        $member = PlayerManager::getInstance()->getPlayer($player->getUniqueId());
        if ($member !== null) {
            $oldClaim = ClaimsManager::getInstance()->getClaim($player->getLevel(), $player->getLevel()->getChunkAtPosition($event->getFrom()));
            $newClaim = ClaimsManager::getInstance()->getClaim($player->getLevel(), ($newChunk = $player->getLevel()->getChunkAtPosition($event->getTo())));
            if ($oldClaim !== $newClaim) {
                if (($faction = $member->getFaction()) !== null && $member->isAutoClaiming()) {
                    if (floor($faction->getPower() / $this->plugin->getConfig()->getNested("factions.claim.cost", 1)) > ($total = count(ClaimsManager::getInstance()->getFactionClaims($faction)))) {
                        if ($total < ($max = $this->plugin->getConfig()->getNested("factions.claims.max", -1)) || $max === -1) {
                            if ($newClaim === null) {
                                $this->plugin->getClaimsManager()->createClaim($faction, $player->getLevel(), $newChunk);
                            } else {
                                if ($this->plugin->getConfig()->getNested("factions.claim.overclaim", true) && $newClaim->canBeOverClaimed() && $newClaim->getFaction()->getId() !== $faction->getId()) {
                                    $newClaim->setFaction($faction);
                                }
                            }
                        }
                    }
                }

                $language = LanguageManager::getInstance()->getPlayerLanguage($player);
                $oldFaction = $oldClaim === null ? null : $oldClaim->getFaction();
                $newFaction = $newClaim === null ? null : $newClaim->getFaction();
                if ($oldFaction !== $newFaction) {
                    if ($member->isFlying()) {
                        if ($newFaction === null || ($newFaction !== $faction && !$faction->isAllied($newFaction))) {
                            $this->plugin->getScheduler()->scheduleRepeatingTask(new DisableFlightTask($player, $member), 20);
                        }
                    }

                    if ($newClaim === null) {
                        $player->addTitle(LanguageManager::getInstance()->getMessage($language, "territory-titles.wilderness-title"), LanguageManager::getInstance()->getMessage($language, "territory-titles.wilderness-subtitle"), 5, 60, 5);
                    } else {
                        $tags = [
                            "{RELATION}" => LanguageManager::getInstance()->getColorFor($player, $newFaction),
                            "{FACTION}" => $newFaction->getName(),
                            "{DESCRIPTION}" => $newFaction->getDescription()
                        ];

                        $player->addTitle(LanguageManager::getInstance()->getMessage($language, "territory-titles.faction-title", $tags), LanguageManager::getInstance()->getMessage($language, "territory-titles.faction-subtitle", $tags), 5, 60, 5);
                    }
                }
            }
        }
    }

    public function canAffectArea(Player $player, Position $position, string $type = FactionPermission::BUILD): bool
    {
        $member = PlayerManager::getInstance()->getPlayer($player->getUniqueId());
        $claim = ($chunk = $position->getLevel()->getChunkAtPosition($position)) === null ? null : $this->manager->getClaim($position->getLevel(), $chunk);
        if ($claim !== null) {
            return $claim->getFaction()->hasPermission($member, $type);
        }
        return true;
    }
}