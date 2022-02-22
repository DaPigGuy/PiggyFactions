<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\addons\scorehud;

use DaPigGuy\PiggyFactions\event\management\FactionCreateEvent;
use DaPigGuy\PiggyFactions\event\management\FactionDisbandEvent;
use DaPigGuy\PiggyFactions\event\management\FactionRenameEvent;
use DaPigGuy\PiggyFactions\event\member\FactionJoinEvent;
use DaPigGuy\PiggyFactions\event\member\FactionLeaveEvent;
use DaPigGuy\PiggyFactions\event\member\PowerChangeEvent;
use DaPigGuy\PiggyFactions\event\role\FactionRoleChangeEvent;
use DaPigGuy\PiggyFactions\PiggyFactions;
use Ifera\ScoreHud\event\TagsResolveEvent;
use pocketmine\event\Listener;

class ScoreHudListener implements Listener
{
    public function __construct(private PiggyFactions $plugin)
    {
    }

    public function onTagResolve(TagsResolveEvent $event): void
    {
        if (($member = $this->plugin->getPlayerManager()->getPlayer($event->getPlayer())) === null) return;
        $faction = $member->getFaction();
        $tag = $event->getTag();
        switch ($tag->getName()) {
            case ScoreHudTags::FACTION:
                $tag->setValue($faction === null ? "N/A" : $faction->getName());
                break;
            case ScoreHudTags::FACTION_RANK:
                $tag->setValue($faction === null ? "N/A" : $member->getRole());
                break;
            case ScoreHudTags::FACTION_POWER:
                $tag->setValue($faction === null ? "N/A" : (string)round($faction->getPower(), 2, PHP_ROUND_HALF_DOWN));
                break;
        }
    }

    public function onFactionCreate(FactionCreateEvent $event): void
    {
        if ($event->isCancelled()) return;
        $player = $event->getPlayer();
        $member = $this->plugin->getPlayerManager()->getPlayer($event->getPlayer());
        ScoreHudManager::getInstance()->updatePlayerTags($player,
            $event->getName(),
            $this->plugin->getLanguageManager()->getMessage($member->getLanguage(), "role.leader"),
            $member->getPower()
        );
    }

    public function onFactionDisband(FactionDisbandEvent $event): void
    {
        if ($event->isCancelled()) return;
        $players = $event->getFaction()->getOnlineMembers();
        foreach ($players as $player) {
            ScoreHudManager::getInstance()->updatePlayerTags($player);
        }
    }

    public function onFactionJoin(FactionJoinEvent $event): void
    {
        if ($event->isCancelled()) return;
        $member = $event->getMember();
        ScoreHudManager::getInstance()->updatePlayerTags(ScoreHudManager::getInstance()->getPlayer($member),
            $event->getFaction()->getName(),
            $this->plugin->getLanguageManager()->getMessage($member->getLanguage(), "role.recruit"),
            $member->getPower()
        );
    }

    public function onFactionLeave(FactionLeaveEvent $event): void
    {
        if ($event->isCancelled()) return;
        ScoreHudManager::getInstance()->updatePlayerTags(ScoreHudManager::getInstance()->getPlayer($event->getMember()));
    }

    public function onFactionRename(FactionRenameEvent $event): void
    {
        if ($event->isCancelled()) return;
        ScoreHudManager::getInstance()->updatePlayerFactionTag(ScoreHudManager::getInstance()->getPlayer($event->getMember()), $event->getName());
    }

    public function onPowerChange(PowerChangeEvent $event): void
    {
        if ($event->isCancelled()) return;
        $member = $event->getMember();
        $faction = $member->getFaction();
        if ($faction === null) return;
        ScoreHudManager::getInstance()->updatePlayerFactionPowerTag(ScoreHudManager::getInstance()->getPlayer($member), $faction->getPower() + ($event->getPower() - $member->getPower()));
    }

    public function onFactionRoleChange(FactionRoleChangeEvent $event): void
    {
        if ($event->isCancelled()) return;
        ScoreHudManager::getInstance()->updatePlayerFactionRankTag(ScoreHudManager::getInstance()->getPlayer($event->getMember()), $event->getNewRole());
    }
}