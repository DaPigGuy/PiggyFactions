<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\addons\scorehud;

use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use Ifera\ScoreHud\event\PlayerTagsUpdateEvent;
use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use pocketmine\player\Player;

class ScoreHudManager
{
    private static ScoreHudManager $instance;

    public function __construct(private PiggyFactions $plugin)
    {
        self::$instance = $this;
    }

    public static function getInstance(): ScoreHudManager
    {
        return self::$instance;
    }

    public function getPlayer(FactionsPlayer $member): ?Player
    {
        return $this->plugin->getServer()->getPlayerByUUID($member->getUuid());
    }

    public function updatePlayerTags(Player $player, string $faction = null, string $rank = null, float $power = null): void
    {
        (new PlayerTagsUpdateEvent($player, [
            new ScoreTag(ScoreHudTags::FACTION, $faction ?? "N/A"),
            new ScoreTag(ScoreHudTags::FACTION_RANK, $rank ?? "N/A"),
            new ScoreTag(ScoreHudTags::FACTION_POWER, $power === null ? "N/A" : (string)round($power, 2, PHP_ROUND_HALF_DOWN))
        ]))->call();
    }

    public function updatePlayerFactionTag(Player $player, string $faction = null): void
    {
        (new PlayerTagUpdateEvent($player, new ScoreTag(ScoreHudTags::FACTION, $faction ?? "N/A")))->call();
    }

    public function updatePlayerFactionRankTag(Player $player, string $rank = null): void
    {
        (new PlayerTagUpdateEvent($player, new ScoreTag(ScoreHudTags::FACTION_RANK, $rank ?? "N/A")))->call();
    }

    public function updatePlayerFactionPowerTag(Player $player, float $power = null): void
    {
        (new PlayerTagUpdateEvent($player, new ScoreTag(ScoreHudTags::FACTION_POWER, $power === null ? "N/A" : (string)round($power, 2, PHP_ROUND_HALF_DOWN))))->call();
    }
}