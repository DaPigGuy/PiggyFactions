<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\players;

use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\PiggyFactions;
use pocketmine\Player;
use pocketmine\utils\UUID;

class PlayerManager
{
    /** @var PiggyFactions */
    private $plugin;

    /** @var self */
    private static $instance;

    /** @var FactionsPlayer[] */
    private $players = [];

    public function __construct(PiggyFactions $plugin)
    {
        self::$instance = $this;

        $this->plugin = $plugin;
        $plugin->getDatabase()->executeSelect("piggyfactions.players.load", [], function (array $rows): void {
            foreach ($rows as $row) {
                $this->players[$row["uuid"]] = new FactionsPlayer(UUID::fromString($row["uuid"]), $row["username"], $row["faction"], $row["role"], $row["power"]);
            }
            $this->plugin->getLogger()->debug("Loaded " . count($rows) . " players");
        });
    }

    public static function getInstance(): PlayerManager
    {
        return self::$instance;
    }

    public function createPlayer(Player $player): FactionsPlayer
    {
        $this->plugin->getDatabase()->executeInsert("piggyfactions.players.create", [
            "uuid" => $player->getUniqueId()->toString(),
            "username" => $player->getName(),
            "faction" => null,
            "role" => null,
            "power" => PiggyFactions::getInstance()->getConfig()->getNested("factions.power.default", 20)
        ]);
        $this->players[$player->getUniqueId()->toString()] = new FactionsPlayer($player->getUniqueId(), $player->getName(), null, null, PiggyFactions::getInstance()->getConfig()->getNested("factions.power.default", 20));
        return $this->players[$player->getUniqueId()->toString()];
    }

    public function getPlayer(UUID $uuid): ?FactionsPlayer
    {
        return $this->players[$uuid->toString()] ?? null;
    }

    public function getPlayerByName(string $name): ?FactionsPlayer
    {
        foreach ($this->players as $player) {
            if (strtolower($player->getUsername()) === strtolower($name)) return $player;
        }
        return null;
    }

    public function getPlayerFaction(UUID $uuid): ?Faction
    {
        return $this->getPlayer($uuid)->getFaction();
    }

    public function areAlliedOrTruced(Player $a, Player $b): bool
    {
        $factionA = $this->getPlayerFaction($a->getUniqueId());
        $factionB = $this->getPlayerFaction($b->getUniqueId());
        if ($factionA === null || $factionB === null) return false;
        if ($factionA->isAllied($factionB) || $factionA->isTruced($factionB)) return true;
        if ($factionA->getId() === $factionB->getId()) return true;
        return false;
    }
}