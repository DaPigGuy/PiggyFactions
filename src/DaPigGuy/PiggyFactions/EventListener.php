<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions;

use DaPigGuy\PiggyFactions\event\member\PowerChangeEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\flags\Flag;
use DaPigGuy\PiggyFactions\utils\ChatTypes;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\Player;

class EventListener implements Listener
{
    /** @var PiggyFactions */
    private $plugin;

    public function __construct(PiggyFactions $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @priority MONITOR
     */
    public function onChat(PlayerChatEvent $event): void
    {
        $player = $event->getPlayer();
        $member = $this->plugin->getPlayerManager()->getPlayer($player);
        if ($member !== null) {
            $faction = $member->getFaction();
            if ($faction !== null) {
                $placeholders = [
                    "{PLAYER}" => $player->getDisplayName(),
                    "{FACTION}" => $faction->getName(),
                    "{RANK_NAME}" => $this->plugin->getTagManager()->getPlayerRankName($member),
                    "{RANK_SYMBOL}" => $this->plugin->getTagManager()->getPlayerRankSymbol($member),
                    "{MESSAGE}" => $event->getMessage()
                ];
                switch ($member->getCurrentChat()) {
                    case ChatTypes::ALLY:
                        $event->setRecipients(array_merge($faction->getOnlineMembers(), ...array_map(function (Faction $ally): array {
                            return $ally->getOnlineMembers();
                        }, $faction->getAllies())));
                        $event->setFormat($this->plugin->getLanguageManager()->getMessage($this->plugin->getLanguageManager()->getDefaultLanguage(), "chat.ally", $placeholders));
                        break;
                    case ChatTypes::FACTION:
                        $event->setRecipients($faction->getOnlineMembers());
                        $event->setFormat($this->plugin->getLanguageManager()->getMessage($this->plugin->getLanguageManager()->getDefaultLanguage(), "chat.faction", $placeholders));
                        break;
                }
            }
        }
    }

    public function onDamageByEntity(EntityDamageByEntityEvent $event): void
    {
        $entity = $event->getEntity();
        $damager = $event->getDamager();
        if ($entity instanceof Player && $damager instanceof Player) {
            if ($this->plugin->getPlayerManager()->areAlliedOrTruced($entity, $damager)) {
                $event->setCancelled();
                return;
            }

            $entityFaction = $this->plugin->getPlayerManager()->getPlayerFaction($entity->getUniqueId());
            $damagerFaction = $this->plugin->getPlayerManager()->getPlayerFaction($damager->getUniqueId());
            if (($entityFaction === null || $damagerFaction === null) && !$this->plugin->getConfig()->getNested("factions.pvp.factionless", true)) {
                $event->setCancelled();
                if ($damagerFaction === null) {
                    $this->plugin->getLanguageManager()->sendMessage($damager, "pvp.attacker-factionless");
                } else {
                    $this->plugin->getLanguageManager()->sendMessage($damager, "pvp.target-factionless");
                }
                return;
            }
            if ($entityFaction === null && $damagerFaction === null && !$this->plugin->getConfig()->getNested("factions.pvp.between-factionless", true)) {
                $event->setCancelled();
                return;
            }

            $claim = $this->plugin->getClaimsManager()->getClaimByPosition($entity);
            if ($claim !== null) {
                if ($claim->getFaction() === $entityFaction) {
                    if ($damagerFaction === null || !$damagerFaction->isEnemy($entityFaction)) {
                        $event->setCancelled();
                        $this->plugin->getLanguageManager()->sendMessage($damager, "pvp.cant-attack-in-territory", ["{PLAYER}" => $entity->getDisplayName()]);
                        return;
                    }
                    $event->setModifier(-$this->plugin->getConfig()->getNested("factions.claims.shield-factor", 0.1), 56789);
                } elseif ($claim->getFaction()->getFlag(Flag::SAFEZONE)) {
                    $event->setCancelled();
                }
            }
        }
    }

    public function onDeath(PlayerDeathEvent $event): void
    {
        $player = $event->getPlayer();
        $member = $this->plugin->getPlayerManager()->getPlayer($player);
        if ($member !== null) {
            $ev = new PowerChangeEvent($member, PowerChangeEvent::CAUSE_DEATH, $member->getPower() + $this->plugin->getConfig()->getNested("factions.power.per.death", -2));
            $ev->call();
            if ($ev->isCancelled()) return;
            $member->setPower($ev->getPower());
            $member->sendMessage("death.power", ["{POWER}" => round($member->getPower(), 2, PHP_ROUND_HALF_DOWN)]);
        }

        $cause = $player->getLastDamageCause();
        if ($cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();
            if ($damager instanceof Player) {
                $damagerMember = $this->plugin->getPlayerManager()->getPlayer($damager);

                if ($damagerMember !== null) {
                    $ev = new PowerChangeEvent($damagerMember, PowerChangeEvent::CAUSE_KILL, $damagerMember->getPower() + $this->plugin->getConfig()->getNested("factions.power.per.kill", 1));
                    $ev->call();
                    if ($ev->isCancelled()) return;
                    $damagerMember->setPower($ev->getPower());
                }
            }
        }
    }

    public function onJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        if (($member = $this->plugin->getPlayerManager()->getPlayer($player)) === null) $member = $this->plugin->getPlayerManager()->createPlayer($player);
        if ($member->getUsername() !== $player->getName()) $member->setUsername($player->getName());
        if (($faction = $member->getFaction()) !== null) {
            if (($motd = $faction->getMotd()) !== null) $member->sendMessage("motd", ["{MOTD}" => $motd]);
        }
    }

    public function onRespawn(PlayerRespawnEvent $event): void
    {
        $player = $event->getPlayer();
        $faction = $this->plugin->getPlayerManager()->getPlayerFaction($player->getUniqueId());
        if ($this->plugin->getConfig()->getNested("factions.homes.teleport-on-death") && $faction !== null && $faction->getHome() !== null) {
            $event->setRespawnPosition($faction->getHome());
        }
    }
}