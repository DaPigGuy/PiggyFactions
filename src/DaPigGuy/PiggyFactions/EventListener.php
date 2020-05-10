<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions;

use DaPigGuy\PiggyFactions\claims\ClaimsManager;
use DaPigGuy\PiggyFactions\event\member\PowerChangeEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\flags\Flag;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\PlayerManager;
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
        $member = PlayerManager::getInstance()->getPlayer($player->getUniqueId());
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
                    $event->setFormat(LanguageManager::getInstance()->getMessage(LanguageManager::DEFAULT_LANGUAGE, "chat.ally", $placeholders));
                    break;
                case ChatTypes::FACTION:
                    $event->setRecipients($faction->getOnlineMembers());
                    $event->setFormat(LanguageManager::getInstance()->getMessage(LanguageManager::DEFAULT_LANGUAGE, "chat.faction", $placeholders));
                    break;
            }
        }
    }

    public function onDamageByEntity(EntityDamageByEntityEvent $event): void
    {
        $entity = $event->getEntity();
        $damager = $event->getDamager();
        if ($entity instanceof Player && $damager instanceof Player) {
            if (PlayerManager::getInstance()->areAlliedOrTruced($entity, $damager)) {
                $event->setCancelled();
                return;
            }

            $entityFaction = PlayerManager::getInstance()->getPlayerFaction($entity->getUniqueId());
            $damagerFaction = PlayerManager::getInstance()->getPlayerFaction($damager->getUniqueId());
            if (($entityFaction === null || $damagerFaction === null) && !$this->plugin->getConfig()->getNested("factions.pvp.factionless", true)) {
                $event->setCancelled();
                if ($damagerFaction === null) {
                    LanguageManager::getInstance()->sendMessage($damager, "pvp.attacker-factionless");
                } else {
                    LanguageManager::getInstance()->sendMessage($damager, "pvp.target-factionless");
                }
                return;
            }
            if ($entityFaction === null && $damagerFaction === null && !$this->plugin->getConfig()->getNested("factions.pvp.between-factionless", true)) {
                $event->setCancelled();
                return;
            }

            $claim = ClaimsManager::getInstance()->getClaim($entity->getLevel(), $entity->getLevel()->getChunkAtPosition($entity));
            if ($claim !== null) {
                if ($claim->getFaction() === $entityFaction) {
                    if ($damagerFaction === null || !$damagerFaction->isEnemy($entityFaction)) {
                        $event->setCancelled();
                        LanguageManager::getInstance()->sendMessage($damager, "pvp.cant-attack-in-territory", ["{PLAYER}" => $entity->getDisplayName()]);
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
        $member = PlayerManager::getInstance()->getPlayer($player->getUniqueId());

        $ev = new PowerChangeEvent($member, PowerChangeEvent::CAUSE_DEATH, $member->getPower() + $this->plugin->getConfig()->getNested("factions.power.per.death", -2));
        $ev->call();
        if ($ev->isCancelled()) return;
        $member->setPower($ev->getPower());
        LanguageManager::getInstance()->sendMessage($player, "death.power", ["{POWER}" => round($member->getPower(), 2, PHP_ROUND_HALF_DOWN)]);
    }

    public function onJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        if (($member = PlayerManager::getInstance()->getPlayer($player->getUniqueId())) === null) $member = PlayerManager::getInstance()->createPlayer($player);
        if ($member->getUsername() !== $player->getName()) $member->setUsername($player->getName());
        LanguageManager::getInstance()->setPlayerLanguage($player, LanguageManager::LANGUAGES[$player->getLocale()] ?? LanguageManager::DEFAULT_LANGUAGE);
        if (($faction = $member->getFaction()) !== null) {
            if (($motd = $faction->getMotd()) !== null) LanguageManager::getInstance()->sendMessage($player, "motd", ["{MOTD}" => $motd]);
        }
    }

    public function onRespawn(PlayerRespawnEvent $event): void
    {

        $player = $event->getPlayer();
        $faction = PlayerManager::getInstance()->getPlayerFaction($player->getUniqueId());
        if ($this->plugin->getConfig()->getNested("factions.homes.teleport-on-death") && $faction !== null && $faction->getHome() !== null) {
            $event->setRespawnPosition($faction->getHome());
        }
    }
}