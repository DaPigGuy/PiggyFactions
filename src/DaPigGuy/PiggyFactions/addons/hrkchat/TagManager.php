<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\addons\hrkchat;

use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\utils\RoundValue;
use pocketmine\player\Player;

class TagManager
{
    private array $rankSymbols;

    private string $factionless;
    private string $powerless;

    public function __construct(private PiggyFactions $plugin)
    {
        $config = $plugin->getConfig()->getNested("symbols", []);
        $this->rankSymbols = $config["ranks"] ?? [];
        $this->factionless = $config["factionless"] ?? "";
        $this->powerless = $config["powerless"] ?? "";

        if (($hrkchat = $plugin->getServer()->getPluginManager()->getPlugin("HRKChat")) !== null && $hrkchat->isEnabled()) $plugin->getServer()->getPluginManager()->registerEvents(new HRKChatTagListener($this), $plugin);
    }

    public function getHRKTag(Player $player, string $tag): ?string
    {
        $tags = explode('.', $tag, 2);
        if ($tags[0] !== 'piggyfacs' || count($tags) < 2) return null;

        $member = $this->plugin->getPlayerManager()->getPlayer($player);
        return match ($tags[1]) {
            "name" => $this->getFactionName($member) ?? $this->factionless,
            "power" => $this->getFactionPower($member) ?? $this->powerless,
            "rank.name" => $this->getPlayerRankName($member) ?? $this->rankSymbols["none"] ?? "",
            "rank.symbol" => $this->getPlayerRankSymbol($member) ?? $this->rankSymbols["none"] ?? "",
            "members.all" => $this->getFactionSizeTotal($member) ?? $this->powerless,
            "members.online" => $this->getFactionSizeOnline($member) ?? $this->powerless,
            default => null,
        };
    }

    public function getFaction(?FactionsPlayer $member): ?Faction
    {
        return $member?->getFaction();
    }

    public function getFactionName(?FactionsPlayer $member): ?string
    {
        return ($faction = $this->getFaction($member)) === null ? null : $faction->getName();
    }

    public function getFactionPower(?FactionsPlayer $member): ?string
    {
        return ($faction = $this->getFaction($member)) === null ? null : RoundValue::roundToString($faction->getPower());
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
        return $this->getFaction($member) === null ? null : (string)$member->getRole();
    }

    public function getPlayerRankSymbol(?FactionsPlayer $member): ?string
    {
        return $this->getFaction($member) === null ? null : ($this->rankSymbols[$member->getRole()] ?? null);
    }
}
