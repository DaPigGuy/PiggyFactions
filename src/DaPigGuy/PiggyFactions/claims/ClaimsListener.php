<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\claims;

use DaPigGuy\PiggyFactions\event\claims\ChunkOverclaimEvent;
use DaPigGuy\PiggyFactions\event\claims\ClaimChunkEvent;
use DaPigGuy\PiggyFactions\event\claims\UnclaimChunkEvent;
use DaPigGuy\PiggyFactions\permissions\FactionPermission;
use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\utils\Relations;
use pocketmine\block\tile\Container;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\player\Player;
use pocketmine\world\format\Chunk;
use pocketmine\world\Position;

class ClaimsListener implements Listener
{
    public function __construct(private PiggyFactions $plugin, private ClaimsManager $manager)
    {
    }

    public function onBreak(BlockBreakEvent $event): void
    {
        if (!$this->canAffectArea($event->getPlayer(), $event->getBlock()->getPosition())) $event->cancel();
    }

    public function onPlace(BlockPlaceEvent $event): void
    {
        if (!$this->canAffectArea($event->getPlayer(), $event->getBlock()->getPosition())) $event->cancel();
    }

    public function onCommandPreprocess(PlayerCommandPreprocessEvent $event): void
    {
        $player = $event->getPlayer();
        $message = $event->getMessage();
        $member = $this->plugin->getPlayerManager()->getPlayer($player);
        if ($member !== null) {
            $faction = $member->getFaction();
            $claim = $this->manager->getClaimByPosition($player->getPosition());
            if (!$member->isInAdminMode() && $claim !== null && $claim->getFaction() !== $faction) {
                $relation = $faction === null ? Relations::NONE : $faction->getRelation($claim->getFaction());
                if (str_starts_with($message, "/")) {
                    $command = substr(explode(" ", $message)[0], 1);
                    if (in_array($command, $this->plugin->getConfig()->getNested("factions.claims.denied-commands." . $relation, []))) {
                        $member->sendMessage("claims.command-denied", ["{COMMAND}" => $command, "{RELATION}" => $relation === "none" ? "neutral" : $relation]);
                        $event->cancel();
                    }
                }
            }
        }
    }

    public function onInteract(PlayerInteractEvent $event): void
    {
        $tile = $event->getBlock()->getPosition()->getWorld()->getTile($event->getBlock()->getPosition());
        if (!$this->canAffectArea($event->getPlayer(), $event->getBlock()->getPosition(), $tile instanceof Container ? FactionPermission::CONTAINERS : FactionPermission::INTERACT)) $event->cancel();
    }

    public function onMove(PlayerMoveEvent $event): void
    {
        $player = $event->getPlayer();
        $member = $this->plugin->getPlayerManager()->getPlayer($player);
        if ($member !== null) {
            $oldClaim = $this->manager->getClaimByPosition($event->getFrom());
            $newClaim = $this->manager->getClaimByPosition($event->getTo());
            if ($oldClaim !== $newClaim) {
                if (($faction = $member->getFaction()) !== null) {
                    if ($member->isAutoClaiming()) {
                        if (!$member->isInAdminMode()) {
                            if (($total = count($this->manager->getFactionClaims($faction))) >= ($max = $this->plugin->getConfig()->getNested("factions.claims.max", -1)) && $max !== -1) {
                                $member->setAutoClaiming(false);
                                $member->sendMessage("commands.claim.auto.toggled-off");
                                $member->sendMessage("commands.claim.max-claimed");
                                return;
                            }
                            if ($total >= floor($faction->getPower() / $this->plugin->getConfig()->getNested("factions.claim.cost", 1))) {
                                $member->setAutoClaiming(false);
                                $member->sendMessage("commands.claim.auto.toggled-off");
                                $member->sendMessage("commands.claim.no-power");
                                return;
                            }
                        }
                        if ($newClaim === null) {
                            $newChunkX = $event->getTo()->getFloorX() >> Chunk::COORD_BIT_SIZE;
                            $newChunkZ = $event->getTo()->getFloorZ() >> Chunk::COORD_BIT_SIZE;
                            $ev = new ClaimChunkEvent($faction, $member, $newChunkX, $newChunkZ);
                            $ev->call();
                            if (!$ev->isCancelled()) {
                                $newClaim = $this->manager->createClaim($faction, $player->getWorld(), $newChunkX, $newChunkZ);
                                $member->sendMessage("commands.claim.success");
                            }
                        } else {
                            if ($newClaim->canBeOverClaimed() && $newClaim->getFaction()->getId() !== $faction->getId()) {
                                $ev = new ChunkOverclaimEvent($faction, $member, $newClaim);
                                $ev->call();
                                if (!$ev->isCancelled()) {
                                    $newClaim->setFaction($faction);
                                    $member->sendMessage("commands.claim.over-claimed");
                                }
                            }
                        }
                    } elseif ($member->isAutoUnclaiming()) {
                        if ($newClaim !== null && ($member->isInAdminMode() || ($newClaim->getFaction()->getId() === $faction->getId() && $faction->hasPermission($member, FactionPermission::UNCLAIM)))) {
                            $ev = new UnclaimChunkEvent($newClaim->getFaction(), $member, $newClaim);
                            $ev->call();
                            if (!$ev->isCancelled()) {
                                $this->manager->deleteClaim($newClaim);
                                $member->sendMessage("commands.unclaim.success");
                            }
                        }
                    }
                }

                $language = $member->getLanguage();
                $oldFaction = $oldClaim?->getFaction();
                $newFaction = $newClaim?->getFaction();
                if ($oldFaction !== $newFaction) {
                    if ($newClaim === null) {
                        $player->sendTitle($this->plugin->getLanguageManager()->getMessage($language, "territory-titles.wilderness-title"), $this->plugin->getLanguageManager()->getMessage($language, "territory-titles.wilderness-subtitle"), 5, 60, 5);
                    } else {
                        $tags = [
                            "{RELATION}" => $this->plugin->getLanguageManager()->getColorFor($player, $newFaction),
                            "{FACTION}" => $newFaction->getName(),
                            "{DESCRIPTION}" => $newFaction->getDescription()
                        ];

                        $player->sendTitle($this->plugin->getLanguageManager()->getMessage($language, "territory-titles.faction-title", $tags), $this->plugin->getLanguageManager()->getMessage($language, "territory-titles.faction-subtitle", $tags), 5, 60, 5);
                    }
                }
            }
        }
    }

    public function canAffectArea(Player $player, Position $position, string $type = FactionPermission::BUILD): bool
    {
        $member = $this->plugin->getPlayerManager()->getPlayer($player);
        $claim = $this->manager->getClaimByPosition($position);
        if ($claim !== null) return $member !== null && $claim->getFaction()->hasPermission($member, $type);
        return true;
    }
}