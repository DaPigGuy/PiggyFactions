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
    private $rankSymbols;

    /** @var string */
    private $factionless;
    /** @var string */
    private $powerless;

    public function __construct(PiggyFactions $plugin)
    {
        $this->plugin = $plugin;

        $config = $plugin->getConfig()->getNested("symbols", []);
        $this->rankSymbols = $config["ranks"] ?? [];
        $this->factionless = $config["factionless"] ?? "";
        $this->powerless = $config["powerless"] ?? "";

        $plugin->getServer()->getPluginManager()->registerEvents(new TagListener($this), $plugin);
    }

    public function getHRKTag(Player $player, string $tag): ?string
    {
        $tags = explode('.', $tag, 2);
        if ($tags[0] !== 'piggyfacs' || count($tags) < 2) return null;

        $member = PlayerManager::getInstance()->getPlayer($player->getUniqueId());
        switch ($tags[1]) {
            case "name":
                return $this->getFactionName($member) ?? $this->factionless;
            case "power":
                return $this->getFactionPower($member) ?? $this->powerless;
            case "rank.name":
                return $this->getPlayerRankName($member) ?? $this->rankSymbols["none"] ?? "";
            case "rank.symbol":
                return $this->getPlayerRankSymbol($member) ?? $this->rankSymbols["none"] ?? "";
            case "members.all":
                return $this->getFactionSizeTotal($member) ?? $this->powerless;
            case "members.online":
                return $this->getFactionSizeOnline($member) ?? $this->powerless;
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
        return ($faction = $this->getFaction($member)) === null ? null : (string)count($faction->getMembers());
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
        return ($faction = $this->getFaction($member)) === null ? null : ($this->rankSymbols[$member->getRole()] ?? null);
    }
}
