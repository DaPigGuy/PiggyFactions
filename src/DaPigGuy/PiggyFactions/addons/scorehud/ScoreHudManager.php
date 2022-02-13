<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\addons\scorehud;

use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\utils\RoundValue;
use Ifera\ScoreHud\event\PlayerTagsUpdateEvent;
use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use pocketmine\player\Player;

class ScoreHudManager
{
    private PiggyFactions $plugin;
    private static ScoreHudManager $instance;

    public function __construct(PiggyFactions $plugin)
    {
        self::$instance = $this;
        $this->plugin = $plugin;
    }

    public static function getInstance(): ScoreHudManager
    {
        return self::$instance;
    }

    public function getPlayer(FactionsPlayer $member): Player
    {
        return $this->plugin->getServer()->getPlayerByUUID($member->getUuid());
    }

    public function updateAllTags(Player $player, string $faction = null, string $rank = null, float $power = null, float $maxpower = null): void
    {
        (new PlayerTagsUpdateEvent($player, [
            new ScoreTag(ScoreHudTags::FACTION_NAME, $faction ?? "N/A"),
            new ScoreTag(ScoreHudTags::FACTION_LEADER, $leader ?? "N/A"),
            new ScoreTag(ScoreHudTags::FACTION_POWER, $power === null ? "N/A" : RoundValue::roundToString($power)),
            new ScoreTag(ScoreHudTags::FACTION_MAX_POWER, $maxpower === null ? "N/A" : RoundValue::roundToString($maxpower)),
            new ScoreTag(ScoreHudTags::MEMBER_RANK, $rank ?? "N/A")
        ]))->call();
    }

    // Faction

    public function updateFactionTag(Player $player, string $faction = null): void
    {
        (new PlayerTagUpdateEvent($player, new ScoreTag(ScoreHudTags::FACTION_NAME, $faction ?? "N/A")))->call();
    }

    public function updateFactionLeaderTag(Player $player, string $leader = null): void
    {
        (new PlayerTagUpdateEvent($player, new ScoreTag(ScoreHudTags::FACTION_LEADER, $leader ?? "N/A")))->call();
    }

    public function updateFactionPowerTag(Player $player, float $power = null): void
    {
        (new PlayerTagUpdateEvent($player, new ScoreTag(ScoreHudTags::FACTION_POWER, $power === null ? "N/A" : RoundValue::roundToString($power))))->call();
    }

    // Member

    public function updateMemberRankTag(Player $player, string $rank = null): void
    {
        (new PlayerTagUpdateEvent($player, new ScoreTag(ScoreHudTags::MEMBER_RANK, $rank ?? "N/A")))->call();
    }
}