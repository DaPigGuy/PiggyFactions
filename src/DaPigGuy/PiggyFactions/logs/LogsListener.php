<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\logs;

use DaPigGuy\PiggyFactions\event\management\FactionBanEvent;
use DaPigGuy\PiggyFactions\event\management\FactionInviteEvent;
use DaPigGuy\PiggyFactions\event\management\FactionKickEvent;
use DaPigGuy\PiggyFactions\event\management\FactionUnbanEvent;
use DaPigGuy\PiggyFactions\event\member\FactionJoinEvent;
use DaPigGuy\PiggyFactions\event\member\FactionLeaveEvent;
use DaPigGuy\PiggyFactions\event\role\FactionLeadershipTransferEvent;
use DaPigGuy\PiggyFactions\PiggyFactions;
use pocketmine\event\Listener;

class LogsListener implements Listener
{
    /** @var PiggyFactions */
    private $plugin;

    public function __construct(PiggyFactions $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @param FactionBanEvent $event
     * @priority MONITOR
     */
    public function onBan(FactionBanEvent $event): void
    {
        if ($event->isCancelled()) return;
        $factionLog = new FactionLog(FactionLog::BAN, ["bannedBy" => $event->getBannedBy()->getUsername(), "banned" => $event->getMember()->getUsername()]);
        LogsManager::getInstance()->addFactionLog($event->getFaction(), $factionLog);
    }

    /**
     * @param FactionKickEvent $event
     * @priority MONITOR
     */
    public function onKick(FactionKickEvent $event): void
    {
        if ($event->isCancelled()) return;
        $factionLog = new FactionLog(FactionLog::KICK, ["kicker" => $event->getKickedBy()->getUsername(), "kicked" => $event->getMember()->getUsername()]);
        LogsManager::getInstance()->addFactionLog($event->getFaction(), $factionLog);
    }

    /**
     * @param FactionInviteEvent $event
     * @priority MONITOR
     */
    public function onInvite(FactionInviteEvent $event): void
    {
        if ($event->isCancelled()) return;
        $factionLog = new FactionLog(FactionLog::INVITE, ["invitedBy" => $event->getInvitedBy()->getUsername(), "invited" => $event->getInvited()->getName()]);
        LogsManager::getInstance()->addFactionLog($event->getFaction(), $factionLog);
    }

    /**
     * @param FactionJoinEvent $event
     * @priority MONITOR
     */
    public function onJoin(FactionJoinEvent $event): void
    {
        if ($event->isCancelled()) return;
        $factionLog = new FactionLog(FactionLog::JOIN, ["joined" => $event->getMember()->getUsername()]);
        LogsManager::getInstance()->addFactionLog($event->getFaction(), $factionLog);
    }

    /**
     * @param FactionLeaveEvent $event
     * @priority MONITOR
     */
    public function onLeave(FactionLeaveEvent $event): void
    {
        if ($event->isCancelled()) return;
        $factionLog = new FactionLog(FactionLog::LEAVE, ["left" => $event->getMember()->getUsername()]);
        LogsManager::getInstance()->addFactionLog($event->getFaction(), $factionLog);
    }

    /**
     * @param FactionLeadershipTransferEvent $event
     * @priority MONITOR
     */
    public function onLeadershipChange(FactionLeadershipTransferEvent $event): void
    {
        if ($event->isCancelled()) return;
        $factionLog = new FactionLog(FactionLog::LEADER_CHANGE, ["new" => $event->getNew()->getUsername(), "old" => $event->getOld()->getUsername()]);
        LogsManager::getInstance()->addFactionLog($event->getFaction(), $factionLog);
    }

    /**
     * @param FactionUnbanEvent $event
     * @priority MONITOR
     */
    public function onUnban(FactionUnbanEvent $event): void
    {
        if ($event->isCancelled()) return;
        $factionLog = new FactionLog(FactionLog::UNBAN, ["unbannedBy" => $event->getUnbannedBy()->getUsername(), "unbanned" => $event->getMember()->getUsername()]);
        LogsManager::getInstance()->addFactionLog($event->getFaction(), $factionLog);
    }
}