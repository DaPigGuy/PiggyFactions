<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions;

use DaPigGuy\PiggyFactions\chat\ChatManager;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\PlayerManager;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
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
            $entityFaction = $this->plugin->getPlayerManager()->getPlayerFaction($entity->getUniqueId());
            $damagerFaction = $this->plugin->getPlayerManager()->getPlayerFaction($damager->getUniqueId());
            if ($entityFaction !== null && $entityFaction === $damagerFaction) $event->setCancelled();
        }
    }

    public function onJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        if (($member = $this->plugin->getPlayerManager()->getPlayer($player->getUniqueId())) === null) $member = $this->plugin->getPlayerManager()->createPlayer($player);
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