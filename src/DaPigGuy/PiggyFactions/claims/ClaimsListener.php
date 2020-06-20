<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\claims;

use DaPigGuy\PiggyFactions\event\claims\ChunkOverclaimEvent;
use DaPigGuy\PiggyFactions\event\claims\ClaimChunkEvent;
use DaPigGuy\PiggyFactions\event\claims\UnclaimChunkEvent;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\permissions\FactionPermission;
use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\players\PlayerManager;
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
        if ($member !== null) {
            $faction = $member->getFaction();
            $claim = ClaimsManager::getInstance()->getClaimByPosition($player);
            if (!$member->isInAdminMode() && $claim !== null && $claim->getFaction() !== $faction) {
                $relation = $faction === null ? Relations::NONE : $faction->getRelation($claim->getFaction());
                if (substr($message, 0, 1) === "/") {
                    $command = substr(explode(" ", $message)[0], 1);
                    if (in_array($command, $this->plugin->getConfig()->getNested("factions.claims.denied-commands." . $relation, []))) {
                        $member->sendMessage("claims.command-denied", ["{COMMAND}" => $command, "{RELATION}" => $relation === "none" ? "neutral" : $relation]);
                        $event->setCancelled();
                    }
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
            $oldClaim = ClaimsManager::getInstance()->getClaimByPosition($event->getFrom());
            $newClaim = ClaimsManager::getInstance()->getClaimByPosition($event->getTo());
            if ($oldClaim !== $newClaim) {
                if (($faction = $member->getFaction()) !== null) {
                    if ($member->isAutoClaiming()) {
                        if (floor($faction->getPower() / $this->plugin->getConfig()->getNested("factions.claim.cost", 1)) > ($total = count(ClaimsManager::getInstance()->getFactionClaims($faction))) || $member->isInAdminMode()) {
                            if ($member->isInAdminMode() || $total < ($max = $this->plugin->getConfig()->getNested("factions.claims.max", -1)) || $max === -1) {
                                if ($newClaim === null) {
                                    $ev = new ClaimChunkEvent($faction, $member, ($newChunk = $player->getLevel()->getChunkAtPosition($event->getTo()))->getX(), $newChunk->getZ());
                                    $ev->call();
                                    if (!$ev->isCancelled()) $newClaim = $this->plugin->getClaimsManager()->createClaim($faction, $player->getLevel(), $newChunk->getX(), $newChunk->getZ());
                                } else {
                                    if ($newClaim->canBeOverClaimed() && $newClaim->getFaction()->getId() !== $faction->getId()) {
                                        $ev = new ChunkOverclaimEvent($faction, $member, $newClaim);
                                        $ev->call();
                                        if (!$ev->isCancelled()) $newClaim->setFaction($faction);
                                    }
                                }
                            }
                        }
                    } elseif ($member->isAutoUnclaiming()) {
                        if ($newClaim !== null && ($member->isInAdminMode() || ($newClaim->getFaction()->getId() === $faction->getId() && $faction->hasPermission($member, FactionPermission::UNCLAIM)))) {
                            $ev = new UnclaimChunkEvent($newClaim->getFaction(), $member, $newClaim);
                            $ev->call();
                            if (!$ev->isCancelled()) $this->plugin->getClaimsManager()->deleteClaim($newClaim);
                        }
                    }
                }

                $language = $member->getLanguage();
                $oldFaction = $oldClaim === null ? null : $oldClaim->getFaction();
                $newFaction = $newClaim === null ? null : $newClaim->getFaction();
                if ($oldFaction !== $newFaction) {
                    if ($newClaim === null) {
                        $player->sendTitle(LanguageManager::getInstance()->getMessage($language, "territory-titles.wilderness-title"), LanguageManager::getInstance()->getMessage($language, "territory-titles.wilderness-subtitle"), 5, 60, 5);
                    } else {
                        $tags = [
                            "{RELATION}" => LanguageManager::getInstance()->getColorFor($player, $newFaction),
                            "{FACTION}" => $newFaction->getName(),
                            "{DESCRIPTION}" => $newFaction->getDescription()
                        ];

                        $player->sendTitle(LanguageManager::getInstance()->getMessage($language, "territory-titles.faction-title", $tags), LanguageManager::getInstance()->getMessage($language, "territory-titles.faction-subtitle", $tags), 5, 60, 5);
                    }
                }
            }
        }
    }

    public function canAffectArea(Player $player, Position $position, string $type = FactionPermission::BUILD): bool
    {
        $member = PlayerManager::getInstance()->getPlayer($player->getUniqueId());
        $claim = $this->manager->getClaimByPosition($position);
        if ($claim !== null) {
            return $member === null ? false : $claim->getFaction()->hasPermission($member, $type);
        }
        return true;
    }
}