<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\chat;

use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\players\PlayerManager;
use pocketmine\Player;

class TagManager
{
    /** @var PiggyFactions */
    private $plugin;

    public function __construct(PiggyFactions $plugin)
    {
        $this->plugin = $plugin;
        $plugin->getServer()->getPluginManager()->registerEvents(new TagListener($this, $plugin->getConfig()->get("tags", [])), $plugin);
    }

    public function getFactionName(Player $player, string $noFactions): string
    {
        $faction = $this->getFactionClass($player);
        if (!$faction instanceof Faction)
            return $noFactions;
        return $faction->getName();
    }

    public function getFactionPower(Player $player, string $noPower): string
    {
        $faction = $this->getFactionClass($player);
        if (!$faction instanceof Faction)
            return $noPower;
        return $faction->getName();
    }

    public function getFactionSizeTotal(Player $player, string $noPower): string
    {
        $faction = $this->getFactionClass($player);
        if (!$faction instanceof Faction)
            return $noPower;
        return (string)count($faction->getMembers());
    }

    public function getFactionSizeOnline(Player $player, string $noPower): string
    {
        $faction = $this->getFactionClass($player);
        if (!$faction instanceof Faction)
            return $noPower;
        return (string)count($faction->getOnlineMembers());
    }

    public function getPlayerRankName(Player $player, string $noRank): string
    {
        $faction = $this->getPlayerFactionClass($player);
        if (!$faction instanceof FactionsPlayer)
            return $noRank;
        return $faction->getRole() ?? $noRank;
    }

    public function getPlayerRankSymbol(Player $player, array $rankMap, string $noRank): string
    {
        $factionsPlayer = $this->getPlayerFactionClass($player);
        if (!$factionsPlayer instanceof FactionsPlayer)
            return $noRank;
        $role = $factionsPlayer->getRole();
        if ($role === null) return $noRank;
        return $rankMap[$role] ?? $noRank;
    }

    protected function getFactionClass(Player $player): ?Faction
    {
        $factionsPlayer = $this->getPlayerFactionClass($player);
        if (!$factionsPlayer instanceof FactionsPlayer || !(($faction = $factionsPlayer->getFaction()) instanceof Faction))
            return null;
        return $faction;
    }

    protected function getPlayerFactionClass(Player $player): ?FactionsPlayer
    {
        $factionsPlayer = PlayerManager::getInstance()->getPlayer($player->getUniqueId());
        if (!$factionsPlayer instanceof FactionsPlayer)
            return null;
        return $factionsPlayer;
    }
}
