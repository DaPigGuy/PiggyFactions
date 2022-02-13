<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\addons\scorehud;

use DaPigGuy\PiggyFactions\event\management\FactionCreateEvent;
use DaPigGuy\PiggyFactions\event\management\FactionDisbandEvent;
use DaPigGuy\PiggyFactions\event\management\FactionRenameEvent;
use DaPigGuy\PiggyFactions\event\member\FactionJoinEvent;
use DaPigGuy\PiggyFactions\event\member\FactionLeaveEvent;
use DaPigGuy\PiggyFactions\event\member\PowerChangeEvent;
use DaPigGuy\PiggyFactions\event\role\FactionLeadershipTransferEvent;
use DaPigGuy\PiggyFactions\event\role\FactionRoleChangeEvent;
use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\utils\RoundValue;
use Ifera\ScoreHud\event\TagsResolveEvent;
use pocketmine\event\Listener;

class ScoreHudListener implements Listener
{
    private PiggyFactions $plugin;

    public function __construct(PiggyFactions $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onTagResolve(TagsResolveEvent $event): void
    {
        if (($member = $this->plugin->getPlayerManager()->getPlayer($event->getPlayer())) === null) return;
        $faction = $member->getFaction();
        $tag = $event->getTag();
        switch ($tag->getName()) {
            case ScoreHudTags::FACTION_NAME:
                $tag->setValue($faction === null ? "N/A" : $faction->getName());
                break;
            case ScoreHudTags::FACTION_LEADER:
                $tag->setValue($faction === null ? "N/A" : $faction->getLeader());
                break;
            case ScoreHudTags::FACTION_POWER:
                $tag->setValue($faction === null ? "N/A" : RoundValue::roundToString($faction->getPower()));
                break;
            case ScoreHudTags::FACTION_MAX_POWER:
                $tag->setValue($faction === null ? "N/A" : RoundValue::roundToString($faction->getMaxPower()));
                break;
            case ScoreHudTags::MEMBER_NAME:
                $tag->setValue($member->getUsername());
                break;
            case ScoreHudTags::MEMBER_RANK:
                $tag->setValue($member->getRole());
                break;
            case ScoreHudTags::MEMBER_RANK_SYMBOL:
                $tag->setValue($this->plugin->getTagManager()->getPlayerRankSymbol($member));
                break;
            case ScoreHudTags::MEMBER_POWER:
                $tag->setValue(RoundValue::roundToString($member->getPower()));
                break;
            case ScoreHudTags::MEMBER_MAX_POWER:
                $tag->setValue(RoundValue::roundToString($member->getMaxPower()));
                break;
        }
    }

    public function onFactionCreate(FactionCreateEvent $event): void
    {
        if ($event->isCancelled()) return;
        $player = $event->getPlayer();
        $member = $this->plugin->getPlayerManager()->getPlayer($event->getPlayer());
        ScoreHudManager::getInstance()->updateAllTags($player,
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
            ScoreHudManager::getInstance()->updateAllTags($player);
        }
    }

    public function onFactionJoin(FactionJoinEvent $event): void
    {
        if ($event->isCancelled()) return;
        $member = $event->getMember();
        ScoreHudManager::getInstance()->updateAllTags(ScoreHudManager::getInstance()->getPlayer($member),
            $event->getFaction()->getName(),
            $this->plugin->getLanguageManager()->getMessage($member->getLanguage(), "role.recruit"),
            $member->getPower()
        );
    }

    public function onFactionLeave(FactionLeaveEvent $event): void
    {
        if ($event->isCancelled()) return;
        ScoreHudManager::getInstance()->updateAllTags(ScoreHudManager::getInstance()->getPlayer($event->getMember()));
    }

    public function onFactionRename(FactionRenameEvent $event): void
    {
        if ($event->isCancelled()) return;
        ScoreHudManager::getInstance()->updateFactionTag(ScoreHudManager::getInstance()->getPlayer($event->getMember()), $event->getName());
    }

    public function onFactionLeadershipTransfer(FactionLeadershipTransferEvent $event): void
    {
        if ($event->isCancelled()) return;
        $players = $event->getFaction()->getOnlineMembers();
        foreach ($players as $player) {
            ScoreHudManager::getInstance()->updateFactionLeaderTag($player, $event->getNew()->getUsername());
        }
    }

    public function onPowerChange(PowerChangeEvent $event): void
    {
        if ($event->isCancelled()) return;
        $member = $event->getMember();
        $faction = $member->getFaction();
        if ($faction === null) return;
        ScoreHudManager::getInstance()->updateFactionPowerTag(ScoreHudManager::getInstance()->getPlayer($member), $faction->getPower() + ($event->getPower() - $member->getPower()));
    }

    public function onFactionRoleChange(FactionRoleChangeEvent $event): void
    {
        if ($event->isCancelled()) return;
        ScoreHudManager::getInstance()->updateMemberRankTag(ScoreHudManager::getInstance()->getPlayer($event->getMember()), $event->getNewRole());
    }
}