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

    /** @var array */
    private $rankMap = [];

    /** @var string */
    private $noFaction = "";
    /** @var string */
    private $noPower = "";
    /** @var string */
    private $noRank = "";

    public function __construct(PiggyFactions $plugin)
    {
        $this->plugin = $plugin;

        $config = $plugin->getConfig()->get("tags", []);
        if (isset($config["rank-map"])) $this->rankMap = $config["rank-map"];
        if (isset($config["no-faction"])) $this->noFaction = $config["no-faction"];
        if (isset($config["no-power"])) $this->noPower = $config["no-power"];
        if (isset($config["no-rank"])) $this->noRank = $config["no-rank"];

        $plugin->getServer()->getPluginManager()->registerEvents(new TagListener($this), $plugin);
    }

    public function getHRKTag(Player $player, string $tag): ?string
    {
        $tags = explode('.', $tag, 2);
        if ($tags[0] !== 'piggyfacs' || count($tags) < 2) return null;

        $member = PlayerManager::getInstance()->getPlayer($player->getUniqueId());
        switch ($tags[1]) {
            case "name":
                return $this->getFactionName($member) ?? $this->noFaction;
            case "power":
                return $this->getFactionPower($member) ?? $this->noPower;
            case "rank.name":
                return $this->getPlayerRankName($member) ?? $this->noRank;
            case "rank.symbol":
                return $this->getPlayerRankSymbol($member) ?? $this->noRank;
            case "members.all":
                return $this->getFactionSizeTotal($member) ?? $this->noPower;
            case "members.online":
                return $this->getFactionSizeOnline($member) ?? $this->noPower;
            default:
                return null;
        }
    }

    public function getFaction(?FactionsPlayer $member): ?Faction
    {
        return $member === null ? null : $member->getFaction();
    }

    public function getFactionName(?FactionsPlayer $member): ?string
    {
        return ($faction = $this->getFaction($member)) === null ? null : $faction->getName();
    }

    public function getFactionPower(?FactionsPlayer $member): ?string
    {
        return ($faction = $this->getFaction($member)) === null ? null : (string)$faction->getPower();
    }

    public function getFactionSizeTotal(?FactionsPlayer $member): ?string
    {
        return ($faction = $this->getFaction($member)) ? null : (string)count($faction->getMembers());
    }

    public function getFactionSizeOnline(?FactionsPlayer $member): ?string
    {
        return ($faction = $this->getFaction($member)) === null ? null : (string)count($faction->getMembers());
    }

    public function getPlayerRankName(?FactionsPlayer $member): ?string
    {
        return ($faction = $this->getFaction($member)) === null ? null : (string)$member->getRole();
    }

    public function getPlayerRankSymbol(?FactionsPlayer $member): ?string
    {
        return ($faction = $this->getFaction($member)) === null ? null : ($this->rankMap[$member->getRole()] ?? null);
    }
}
