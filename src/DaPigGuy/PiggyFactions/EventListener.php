<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions;

use DaPigGuy\PiggyFactions\chat\ChatManager;
use DaPigGuy\PiggyFactions\claims\ClaimsManager;
use DaPigGuy\PiggyFactions\event\member\PowerChangeEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\PlayerManager;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\LoginPacket;
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
        $faction = PlayerManager::getInstance()->getPlayerFaction($player->getUniqueId());
        if ($faction !== null) {
            switch (ChatManager::getInstance()->getCurrentChat($player)) {
                case ChatManager::ALLY_CHAT:
                    $event->setRecipients(array_merge($faction->getOnlineMembers(), ...array_map(function (Faction $ally): array {
                        return $ally->getOnlineMembers();
                    }, $faction->getAllies())));
                    $event->setFormat(LanguageManager::getInstance()->getMessage(LanguageManager::DEFAULT_LANGUAGE, "chat.ally", [
                        "{PLAYER}" => $player->getDisplayName(),
                        "{FACTION}" => $faction->getName(),
                        "{MESSAGE}" => $event->getMessage()
                    ]));
                    break;
                case ChatManager::FACTION_CHAT:
                    $event->setRecipients($faction->getOnlineMembers());
                    $event->setFormat(LanguageManager::getInstance()->getMessage(LanguageManager::DEFAULT_LANGUAGE, "chat.faction", [
                        "{PLAYER}" => $player->getDisplayName(),
                        "{FACTION}" => $faction->getName(),
                        "{MESSAGE}" => $event->getMessage()
                    ]));
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
            $claim = ClaimsManager::getInstance()->getClaim($entity->getLevel(), $entity->getLevel()->getChunkAtPosition($entity));
            if ($claim !== null) {
                if ($claim->getFaction() === $entityFaction) {
                    if ($damagerFaction === null || !$damagerFaction->isEnemy($entityFaction)) {
                        $event->setCancelled();
                        return;
                    }
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
        if ($member->getUsername() !== $player->getName()) $member->setUsername($member->getUsername());
        if (($faction = $member->getFaction()) !== null) {
            if (($motd = $faction->getMotd()) !== null) LanguageManager::getInstance()->sendMessage($player, "motd", ["{MOTD}" => $motd]);
        }
    }

    public function onDataPacketReceive(DataPacketReceiveEvent $event): void
    {
        $player = $event->getPlayer();
        $packet = $event->getPacket();
        if ($packet instanceof LoginPacket) {
            LanguageManager::getInstance()->setPlayerLanguage($player, LanguageManager::LANGUAGES[$packet->locale] ?? LanguageManager::DEFAULT_LANGUAGE);
        }
    }
}